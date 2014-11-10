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

use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\RepositoryCrawlerBundle\Crawler\AbstractCrawlerContext;

/**
 * Test crawler context.
 */
class TestCrawlerContext extends AbstractCrawlerContext
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearch()
    {
        return new Search();
    }
}
