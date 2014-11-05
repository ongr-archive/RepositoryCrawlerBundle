<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE: 
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace ONGR\RepositoryCrawlerBundle;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;


/**
 * Abstract crawler context with basic processing logic implementation
 */
abstract class AbstractCrawlerContext implements CrawlerContextInterface
{
    /**
     * @var DocumentProcessorInterface[]
     */
    protected $processors;

    /**
     * {@inheritdoc}
     */
    public function addDocumentProcessor(DocumentProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function processData(DocumentInterface $result)
    {
        if ($this->processors === null) {
            throw new \RuntimeException(
                'Crawler context service cannot process documents because no document processors are injected.'
            );
        }

        foreach ($this->processors as $processor) {
            $processor->handleDocument($result);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finalize()
    {
        // Extend this method to add custom logic after documents processing.
    }
}
