<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Functional\Crawler;

use ONGR\RepositoryCrawlerBundle\Crawler\AbstractCrawlerContext;

class AbstractCrawlerContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for processData().
     */
    public function testProcessData()
    {
        $processor = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Crawler\DocumentProcessorInterface');
        $processor->expects($this->once())->method('handleDocument');

        $processor2 = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Crawler\DocumentProcessorInterface');
        $processor2->expects($this->once())->method('handleDocument');

        /** @var \PHPUnit_Framework_MockObject_MockObject|AbstractCrawlerContext $contextService */
        $contextService = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\AbstractCrawlerContext')
            ->setMethods(['getRepository', 'getSearch'])
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
