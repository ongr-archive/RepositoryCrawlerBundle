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
abstract class AbstractCrawlerModifier
{
    /**
     * Processes document.
     *
     * @param DocumentInterface $document
     */
    abstract protected function processData(DocumentInterface $document);

    /**
     * Calls context->process.
     *
     * @param ItemPipelineEvent $documentEvent
     */
    public function onModify(ItemPipelineEvent $documentEvent)
    {
        $this->processData($documentEvent->getItem());
    }
}
