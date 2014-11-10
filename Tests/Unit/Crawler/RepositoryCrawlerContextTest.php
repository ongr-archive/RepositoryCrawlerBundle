<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Unit\Crawler;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\RepositoryCrawlerBundle\Crawler\RepositoryCrawlerContext;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\RepositoryCrawlerBundle;

class RepositoryCrawlerContextTest extends ElasticsearchTestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Repository
     */
    protected function getRepository()
    {
        return $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test for getRepository().
     */
    public function testGetRepository()
    {
        $repository = $this->getRepository();
        $context = new RepositoryCrawlerContext($repository);
        $this->assertInstanceOf('ONGR\ElasticsearchBundle\ORM\Repository', $context->getRepository());
    }

    /**
     * Test for getSearch().
     */
    public function testGetSearch()
    {
        $repository = $this->getRepository();
        $context = new RepositoryCrawlerContext($repository);
        $this->assertInstanceOf('ONGR\ElasticsearchBundle\DSL\Search', $context->getSearch());
    }

    /**
     * Test for finalize().
     *
     * Should not do anything just like its parent.
     */
    public function testFinalize()
    {
        $repository = $this->getRepository();
        $context = new RepositoryCrawlerContext($repository);
        $oldContext = clone $context;

        $context->finalize();
        $this->assertEquals($oldContext, $context);
    }
}
