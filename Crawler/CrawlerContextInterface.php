<?php

namespace ONGR\RepositoryCrawlerBundle;

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
