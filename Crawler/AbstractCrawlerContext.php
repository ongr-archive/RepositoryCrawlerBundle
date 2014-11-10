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

/**
 * Abstract crawler context with basic processing logic implementation.
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
