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
use ONGR\ElasticsearchBundle\Document\DocumentInterface;

/**
 * Abstract class for event listener.
 */
abstract class AbstractCrawlerConsumer
{
    /**
     * Processes document.
     *
     * @param DocumentInterface $document
     */
    protected function processData(DocumentInterface $document)
    {
        // To be implemented by the end user.
    }

    /**
     * Calls context->process.
     *
     * @param ItemPipelineEvent $documentEvent
     */
    public function onConsume(ItemPipelineEvent $documentEvent)
    {
        /** @var CrawlerPipelineContext $eventContext */
        $eventContext = $documentEvent->getContext();

        $this->processData($documentEvent->getItem());

        $eventContext->advanceProgress();
    }
}
