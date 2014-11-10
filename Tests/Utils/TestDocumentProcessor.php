<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Utils;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\RepositoryCrawlerBundle\Crawler\DocumentProcessorInterface;

/**
 * Class TestDocumentProcessor - you know, for tests.
 *
 * @package ONGR\RepositoryCrawlerBundle\Tests
 */
class TestDocumentProcessor implements DocumentProcessorInterface
{
    /**
     * Stores returned documents.
     *
     * @var array documentCollection
     */

    public $documentCollection;

    /**
     * Processes single document.
     *
     * @param DocumentInterface $document
     */

    public function handleDocument(DocumentInterface $document)
    {
        $this->documentCollection[] = $document;
    }
}
