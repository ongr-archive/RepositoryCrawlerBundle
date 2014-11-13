<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Crawler;

use ONGR\RepositoryCrawlerBundle\Event\CrawlerChunkEvent;
use ONGR\RepositoryCrawlerBundle\Event\CrawlerPipelineContext;
use ONGR\ConnectionsBundle\Pipeline\PipelineFactory;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Result\AbstractResultsIterator;
use ONGR\ConnectionsBundle\Pipeline\Pipeline;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Crawler - crawls repository, processes each and every document.
 *
 * @package ONGR\RepositoryCrawlerBundle
 */
class Crawler
{

    /**
     * @var CrawlerContextInterface[]
     */
    protected $contexts;

    /**
     * @var Pipeline chunk pipeline
     */
    protected $pipelineChunk = null;

    /**
     * @var PipelineFactory pipeline factory
     */
    protected $pipelineFactory = null;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Pipeline factory setter.
     *
     * @param PipelineFactory $pipelineFactory
     */
    public function setPipelineFactory(PipelineFactory $pipelineFactory)
    {
        $this->pipelineFactory = $pipelineFactory;
        $this->pipelineFactory->setClassName('ONGR\ConnectionsBundle\Pipeline\Pipeline');
    }

    /**
     * Sets pipeline for chunks.
     *
     * @throws \RuntimeException
     */
    public function setPipelineChunk()
    {
        $this->pipelineChunk = $this->pipelineFactory->create('ongr.repository_crawler.chunkEvent');
    }

    /**
     * Sets console output.
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Adds context service.
     *
     * @param string                  $name
     * @param CrawlerContextInterface $context
     */
    public function addContext($name, CrawlerContextInterface $context)
    {
        $this->contexts[$name] = $context;
    }

    /**
     * Returns context by name.
     *
     * @param string $name
     *
     * @return CrawlerContextInterface
     * @throws \RuntimeException
     */
    protected function getContext($name)
    {
        if (!isset($this->contexts[$name])) {
            throw new \RuntimeException("Context with name '{$name}' does not exist.");
        }

        return $this->contexts[$name];
    }

    /**
     * Gets documents and passes it to crawler context service.
     *
     * @param string $context
     */
    public function run($context)
    {
        $contextService = $this->getContext($context);
        $search = $contextService->getSearch();

        $resultSet = $contextService->getRepository()->execute($search, Repository::RESULTS_OBJECT);

        if ($resultSet !== null) {
            $this->processData($contextService, $resultSet, $this->getProgressHelper($resultSet->count()));
        }
        $contextService->finalize();
    }

    /**
     * Gets chunk of documents and passes it to crawler context service.
     *
     * @param string $context
     * @param string $scrollId
     *
     * @throws \RuntimeException
     */
    public function runAsync($context, $scrollId = null)
    {
        if ($this->pipelineChunk === null) {
            throw new \RuntimeException('Pipeline must be set when running crawler in async mode.');
        }

        $contextService = $this->getContext($context);

        if ($scrollId === null) {
            $resultSet = $contextService->getRepository()->execute(
                $contextService->getSearch(),
                Repository::RESULTS_OBJECT
            );
        } else {
            $resultSet = $contextService->getRepository()->scan(
                $scrollId,
                Search::SCROLL_DURATION,
                Repository::RESULTS_OBJECT
            );
        }

        $this->pipelineChunk->setContext(new CrawlerChunkEvent($resultSet->getScrollId()));
        $this->pipelineChunk->execute();

        $this->processData($contextService, $resultSet, $this->getProgressHelper($resultSet->count()));

        $contextService->finalize();
    }

    /**
     * Creates and returns instance of progress helper.
     *
     * @param int $count
     *
     * @return null|ProgressHelper
     */
    protected function getProgressHelper($count)
    {
        if ($this->output === null) {
            return null;
        }

        if (class_exists('\Symfony\Component\Console\Helper\ProgressBar')) {
            $progress = new ProgressBar($this->output, $count);
            $progress->start();
        } else {
            // This is for backwards compatibility only.
            // @codeCoverageIgnoreStart
            $progress = new ProgressHelper();
            $progress->start($this->output, $count);
            // @codeCoverageIgnoreEnd
        }

        return $progress;
    }

    /**
     * Iterates through result set and passes objects to crawler context service.
     *
     * @param CrawlerContextInterface       $context
     * @param AbstractResultsIterator       $resultSet
     * @param ProgressHelper|ProgressBar    $progress
     */
    protected function processData(CrawlerContextInterface $context, AbstractResultsIterator $resultSet, $progress)
    {
        $pipelineContext = new CrawlerPipelineContext();
        $pipelineContext->setPipeContext($context, $resultSet);

        $pipeline = $this->pipelineFactory->create('ongr.repository_crawler.processEvent');
        $pipeline->setContext($pipelineContext);
        $pipeline->execute();

        if ($progress !== null) {
            $progress->advance($resultSet->count());
        }
    }
}
