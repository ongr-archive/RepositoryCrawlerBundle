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

class CrawlerConsumer {

    /**
     * Calls context->advanceProgress.
     *
     * @param ItemPipelineEvent $documentEvent
     */
    public function onConsume(ItemPipelineEvent $documentEvent)
    {
        /** @var CrawlerPipelineContext $eventContext */
        $eventContext = $documentEvent->getContext();

        $eventContext->advanceProgress();
    }
} 
