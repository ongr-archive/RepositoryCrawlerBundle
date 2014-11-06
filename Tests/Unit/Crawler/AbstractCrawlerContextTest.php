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

use ONGR\RepositoryCrawlerBundle\AbstractCrawlerContext;

class AbstractCrawlerContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for processData().
     */
    public function testProcessData()
    {
        $processor = $this->getMock('ONGR\RepositoryCrawlerBundle\Crawler\DocumentProcessorInterface');
        $processor->expects($this->once())->method('handleDocument');

        $processor2 = $this->getMock('ONGR\RepositoryCrawlerBundle\Crawler\DocumentProcessorInterface');
        $processor2->expects($this->once())->method('handleDocument');

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCrawlerContext $contextService */
        $contextService = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\AbstractCrawlerContext')
            ->setMethods(array('getModel', 'getQuery'))
            ->getMock();

        $contextService->addDocumentProcessor($processor);
        $contextService->addDocumentProcessor($processor2);

        // Tests if handleDocument() was called correctly.
        $document = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $contextService->processData($document);
    }

    /**
     * Test for processData() in case of no processors were injected.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage no document processors are injected
     */
    public function testProcessDataException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCrawlerContext $contextService */
        $contextService = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\AbstractCrawlerContext')
            ->setMethods(['getRepository', 'getSearch'])
            ->getMock();

        $document = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $contextService->processData($document);
    }
}
