<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Fixtures;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\RepositoryCrawlerBundle\Event\AbstractCrawlerModifier;

/**
 * Class TestDocumentProcessor - you know, for tests.
 *
 * @package ONGR\RepositoryCrawlerBundle\Tests
 */
class TestDocumentProcessor extends AbstractCrawlerModifier
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
     * @param mixed $document
     */
    public function processData($document)
    {
        $this->documentCollection[] = $document;
    }
}
