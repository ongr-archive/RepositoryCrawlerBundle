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
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\DSL\Search;

interface CrawlerContextInterface
{
    /**
     * Returns repository instance.
     *
     * @return Repository
     */
    public function getRepository();

    /**
     * Returns search.
     *
     * @return Search
     */
    public function getSearch();

    /**
     * Sets document processor.
     *
     * @param DocumentProcessorInterface $processor
     */
    public function addDocumentProcessor(DocumentProcessorInterface $processor);

    /**
     * Applies all processors for single result.
     *
     * @param DocumentInterface $result
     *
     * @throws \RuntimeException If no processors were injected
     */
    public function processData(DocumentInterface $result);

    /**
     * Execute additional actions after all documents are processed.
     */
    public function finalize();
}
