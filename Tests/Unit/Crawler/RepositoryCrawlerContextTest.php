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

namespace Fox\ConnectionsBundle\Tests\Functional\Crawler;

use ONGR\RepositoryCrawlerBundle\RepositoryCrawlerContext;
use ONGR\ElasticsearchBundle\ORM\Repository;

class RepositoryCrawlerContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Repository
     */
    protected function getSessionModel()
    {
        return $this->getMock('ONGR\ElasticsearchBundle\ORM\Repository');
    }

    /**
     * Test for getModel()
     */
    public function testGetModel()
    {
        $context = new RepositoryCrawlerContext($this->getSessionModel());
        $this->assertInstanceOf('ONGR\ElasticsearchBundle\ORM\Repository', $context->getRepository());
    }

    /**
     * Test for getQuery()
     */
    public function testGetQuery()
    {
        $context = new RepositoryCrawlerContext($this->getSessionModel());
        $this->assertInstanceOf('ONGR\ElasticsearchBundle\DSL\Search', $context->getSearch());
    }

    /**
     * Test for finalize()
     *
     * Shouldn't do anything just like its parent
     */
    public function testFinalize()
    {
        $context = new RepositoryCrawlerContext($this->getSessionModel());
        $oldContext = clone $context;

        $context->finalize();
        $this->assertEquals($oldContext, $context);
    }
}
