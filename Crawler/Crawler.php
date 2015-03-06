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

use ONGR\ConnectionsBundle\Pipeline\PipelineFactory;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Crawler - crawls repository, processes each and every document.
 */
class Crawler
{
    /**
     * @var PipelineFactory Pipeline factory.
     */
    protected $pipelineFactory = null;

    /**
     * @var string
     */
    protected $target = 'default';

    /**
     * @var OutputInterface Console output.
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
    }

    /**
     * @return PipelineFactory
     */
    public function getPipelineFactory()
    {
        return $this->pipelineFactory;
    }

    /**
     * @param OutputInterface $output Sets console output.
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface Gets console output.
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets the event name interfix.
     *
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Gets documents and passes it to crawler context service.
     *
     * @param string $prefix
     */
    public function startCrawler($prefix)
    {
        $pipeline = $this->pipelineFactory->create($prefix . $this->target);
        $pipeline->setProgressBar(new ProgressBar($this->getOutput()));
        $pipeline->start();
    }
}
