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

/**
 * Provides source event handler.
 */
class CrawlerSourceEventHandler
{

    /**
     * On source event - sets sources.
     *
     * @param SourcePipelineEvent $sourceEvent
     */
    public function onSource(SourcePipelineEvent $sourceEvent)
    {
        /** @var CrawlerPipelineContext $pipelineContext */
        $pipelineContext = $sourceEvent->getContext();

        $sourceEvent->setSources([$pipelineContext->getResults()]);
    }
}
