<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Event;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;

/**
 * Listens for processDocument event.
 */
class CrawlerConsumer extends AbstractCrawlerConsumer
{
    /**
     * Processes document. (Ignore code coverage - to be implemented by the end-user.
     *
     * @param DocumentInterface $document
     *
     * @codeCoverageIgnore
     */
    protected function processData(DocumentInterface $document)
    {
        // To be implemented by the end-user.
    }
}
