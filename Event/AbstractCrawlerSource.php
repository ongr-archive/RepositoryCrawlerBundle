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
     * @param SourcePipelineEvent $event
     * @param array|DocumentIterator  $results
     *
     * @throws \InvalidArgumentException
     */
    public function registerSource(SourcePipelineEvent $event, $results)
    {
        /** @var CrawlerPipelineContext $pipelineContext */
        $pipelineContext = $event->getContext();

        $event->addSource($results);

        if (is_array($results)) {
            // CrawlerRepositorySource always returns DocumentIterator, thus this is not tested here.
            // @codeCoverageIgnoreStart
            $pipelineContext->addResults(count($results));
            // @codeCoverageIgnoreEnd
        } elseif (($results instanceof DocumentIterator)) {
            $pipelineContext->addResults($results->count());
        }
    }

    /**
     * Source provider event.
     *
     * @param SourcePipelineEvent $sourceEvent
     */
    abstract public function onSource(SourcePipelineEvent $sourceEvent);
}
