<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use ONGR\ConnectionsBundle\Pipeline\Event\ItemPipelineEvent;

/**
 * Advances progress when applicable.
 */
class CrawlerConsumer
{
    /**
     * Advances the progress (if applicable) in pipeline context. Context must be of type CrawlerPipelineContext).
     *
     * @param ItemPipelineEvent $documentEvent
     *
     * @throws \LogicException
     */
    public function onConsume(ItemPipelineEvent $documentEvent)
    {
        /** @var CrawlerPipelineContext $eventContext */
        $eventContext = $documentEvent->getContext();

        if (!($eventContext instanceof CrawlerPipelineContext)) {
            throw new \LogicException(
                'Crawler consumer only accepts events with CrawlerPipelineContext context.'
            );
        }

        $eventContext->advanceProgress();
    }
}
