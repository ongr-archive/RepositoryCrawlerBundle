<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use ONGR\ConnectionsBundle\Pipeline\Event\SourcePipelineEvent;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;

/**
 * Provides data from Elasticsearch repository.
 */
abstract class AbstractCrawlerSource
{
    /**
     * Registers source.
     *
     * @param SourcePipelineEvent    $event
     * @param array|DocumentIterator $results
     *
     * @throws \InvalidArgumentException
     */
    public function registerSource(SourcePipelineEvent $event, $results)
    {
        /** @var CrawlerPipelineContext $pipelineContext */
        $pipelineContext = $event->getContext();

        if (!($pipelineContext instanceof CrawlerPipelineContext)) {
            throw new \InvalidArgumentException(
                'Crawler source only accepts events with CrawlerPipelineContext context.'
            );
        }

        $event->addSource($results);

        $pipelineContext->addResults(count($results));
    }

    /**
     * Source provider event.
     *
     * @param SourcePipelineEvent $sourceEvent
     */
    abstract public function onSource(SourcePipelineEvent $sourceEvent);
}
