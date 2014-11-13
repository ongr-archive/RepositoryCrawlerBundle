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
 * Listens for processDocument event.
 */
class CrawlerConsumeEventHandler
{
    /**
     * Calls context->process.
     *
     * @param ItemPipelineEvent $documentEvent
     */
    public function onConsume(ItemPipelineEvent $documentEvent)
    {
        /** @var CrawlerPipelineContext $eventContext */
        $eventContext = $documentEvent->getContext();

        $context = $eventContext->getCrawlerContext();

        $context->processData($documentEvent->getItem());
    }
}
