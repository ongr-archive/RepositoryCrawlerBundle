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

use ONGR\ElasticsearchBundle\Document\DocumentInterface;

interface DocumentProcessorInterface
{
    /**
     * Processes single document.
     *
     * @param DocumentInterface $document
     */
    public function handleDocument(DocumentInterface $document);
}
