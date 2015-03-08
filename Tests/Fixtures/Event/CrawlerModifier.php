<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Fixtures\Event;

/**
 * Gets called on each iteration.
 */
class CrawlerModifier extends AbstractCrawlerModifier
{
    /**
     * Constructor.
     *
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Processes documents.
     *
     * @param AbstractDocument $document
     */
    protected function processData($document)
    {
        // Implementation should contain body with action,
        // that will be performed on each iteration with each $document.
    }
}
