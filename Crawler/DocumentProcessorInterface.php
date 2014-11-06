<?php

namespace ONGR\RepositoryCrawlerBundle;

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
