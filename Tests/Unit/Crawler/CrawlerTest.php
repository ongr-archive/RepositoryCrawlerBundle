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

namespace ONGR\RepositoryCrawlerBundle\Tests\Unit\Crawler;

use ONGR\RepositoryCrawlerBundle\Crawler;
use ONGR\RepositoryCrawlerBundle\Tests\Utils\ResultsIteratorBuilder;
use ONGR\ElasticsearchBundle\Result\DocumentScanIterator;
use ONGR\ElasticsearchBundle\DSL\Search;

class CrawlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for run()
     */
    public function testRun()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document2 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $iterator = ResultsIteratorBuilder::getMock($this, array($document1, $document2));

        $sessionModel = $this->getMock('ONGR\ElasticsearchBundle\ORM\Repository');
        $sessionModel->expects($this->once())->method('exportDocuments')->will($this->returnValue($iterator));

        $query = new Search();

        $context = $this->getMock('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface');
        // Test if processData() was called
        $context->expects($this->exactly(2))->method('processData')->with($document1);
        $context->expects($this->once())->method('getModel')->will($this->returnValue($sessionModel));
        $context->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $context->expects($this->once())->method('finalize');

        $crawler = new Crawler();
        $crawler->addContext('test_context', $context);
        $crawler->run('test_context');
    }

    /**
     * Test for run() in case of context exception
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage does not exist
     */
    public function testRunException()
    {
        $crawler = new Crawler();
        $crawler->run('fake_context');
    }

    /**
     * Test for runAsync()
     */
    public function testRunAsync()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document2 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $iterator = ResultsIteratorBuilder::getMock($this, array($document1, $document2), true);
        $iterator->expects($this->once())->method('getScrollId');

        $sessionModel = $this->getMock('ONGR\ElasticsearchBundle\ORM\Repository');
        $sessionModel->expects($this->once())->method('exportDocumentsChunk')->will($this->returnValue($iterator));

        $context = $this->getMock('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface');
        // Test if processData() was called
        $context->expects($this->exactly(2))->method('processData')->with($document1);
        $context->expects($this->once())->method('getModel')->will($this->returnValue($sessionModel));
        $context->expects($this->once())->method('finalize');

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->once())->method('dispatch')->with(
            'fox_connections.crawler.chunk',
            $this->anything()
        );

        $crawler = new Crawler();
        $crawler->setDispatcher($dispatcher);
        $crawler->addContext('test_context', $context);
        $crawler->runAsync('test_context');
    }

    /**
     * Test for runAsync() in case of event dispatcher is not set
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Event dispatcher must be set
     */
    public function testRunAsyncException()
    {
        $iterator = new DocumentScanIterator(null, null, null);

        $sessionModel = $this->getMock('ONGR\ElasticsearchBundle\ORM\Repository');
        $sessionModel->expects($this->once())->method('exportDocumentsChunk')->will($this->returnValue($iterator));

        $context = $this->getMock('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface');
        $context->expects($this->once())->method('getModel')->will($this->returnValue($sessionModel));

        $crawler = new Crawler();
        $crawler->addContext('test_context', $context);

        $crawler->runAsync('test_context');
    }
}
