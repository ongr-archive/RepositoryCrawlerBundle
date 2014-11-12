<?php
/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */


namespace ONGR\RepositoryCrawlerBundle\Event;

use ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface;
use ONGR\ElasticsearchBundle\Result\AbstractResultsIterator;

/**
 * Holds pipeline context for crawler.
 *
 * @package ONGR\RepositoryCrawlerBundle\Event
 */
class CrawlerPipelineContext
{
    /**
     * @var CrawlerContextInterface context
     */
    protected $context;

    /**
     * @var AbstractResultsIterator results
     */
    protected $results;

    /**
     * Sets pipeline context for crawler.
     *
     * @param CrawlerContextInterface $context
     * @param AbstractResultsIterator $results
     */
    public function setPipeContext(CrawlerContextInterface $context, AbstractResultsIterator $results)
    {
        $this->context = $context;
        $this->results = $results;
    }

    /**
     * Returns the CrawlerContext part fo the crawler pipeline context.
     *
     * @return CrawlerContextInterface
     */
    public function getCrawlerContext()
    {
        return $this->context;
    }

    /**
     * Returns the results part of crawler pipeline context.
     *
     * @return AbstractResultsIterator
     */
    public function getResults()
    {
        return $this->results;
    }
} 
