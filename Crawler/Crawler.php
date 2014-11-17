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

use ONGR\RepositoryCrawlerBundle\Event\CrawlerPipelineContext;
use ONGR\ConnectionsBundle\Pipeline\PipelineFactory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Crawler - crawls repository, processes each and every document.
 */
class Crawler
{
    /**
     * Pipeline factory.
     *
     * @var PipelineFactory
     */
    protected $pipelineFactory = null;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $eventNameInterfix = 'default';

    /**
     * Holds all the applicable consumer events.
     *
     * @var array
     */
    protected $consumeEventListeners;

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
     * Sets console output.
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Sets the event name interfix.
     *
     * @param string $eventNameInterfix
     */
    public function setEventNameInterfix($eventNameInterfix)
    {
        $this->eventNameInterfix = $eventNameInterfix;
    }

    /**
     * Set registered consumer event listeners.
     *
     * @param array $listenerList
     */
    public function setConsumeEventListeners($listenerList)
    {
        $this->consumeEventListeners = $listenerList;
    }

    /**
     * Gets documents and passes it to crawler context service.
     */
    public function run()
    {
        $pipeline = $this->pipelineFactory->create('repository_crawler.' . $this->eventNameInterfix);
        $pipelineContext = new CrawlerPipelineContext();
        $pipelineContext->setResultProcessors($pipeline->getName(), $this->consumeEventListeners);
        $pipeline->setContext($pipelineContext);
        $pipeline->execute();
    }
}
