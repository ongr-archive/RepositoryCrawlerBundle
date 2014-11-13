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


use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\RepositoryCrawlerBundle\Tests\Fixtures\ResultsIteratorBuilder;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class CrawlerTest extends ElasticsearchTestCase
{
    /**
     * Test for run().
     */
    public function testRun()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document2 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $iterator = ResultsIteratorBuilder::getMock($this, [$document1, $document2]);
        $iterator->expects($this->any())->method('count')->willReturn($this->returnValue(2));

        $manager = $this->getManager();

        $repository = $this
            ->getMock('ONGR\ElasticsearchBundle\ORM\Repository', [], [$manager, ['AcmeTestBundle:Product']]);
        $repository->expects($this->once())->method('execute')->will($this->returnValue($iterator));

        $context = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface');
        // Test if processData() was called.
        $context->expects($this->exactly(2))->method('processData')->with($document1);
        $context->expects($this->once())->method('getRepository')->will($this->returnValue($repository));
        $context->expects($this->once())->method('getSearch')->will($this->returnValue(new Search()));
        $context->expects($this->once())->method('finalize');

        $crawler = $this->getContainer()->get('ongr.repository_crawler.crawler');
        $crawler->addContext('test_context', $context);
        $crawler->run('test_context');
    }

    /**
     * Test for run() in case of context exception.
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
     * Test for runAsync().
     */
    public function testRunAsync()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document2 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $iterator = ResultsIteratorBuilder::getMock($this, [$document1, $document2], true);
        $iterator->expects($this->exactly(2))->method('getScrollId')->will($this->returnValue('1'));

        $manager = $this->getManager();

        $repository = $this
            ->getMock('ONGR\ElasticsearchBundle\ORM\Repository', [], [$manager, ['AcmeTestBundle:Product']]);
        $repository->expects($this->exactly(1))->method('execute')->will($this->returnValue($iterator));
        $repository->expects($this->exactly(1))->method('scan')->will($this->returnValue($iterator));

        $context = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface');
        $context->expects($this->any())->method('getRepository')->will($this->returnValue($repository));
        $context->expects($this->any())->method('getSearch')->will($this->returnValue(new Search()));
        $context->expects($this->any())->method('finalize');

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher->expects($this->exactly(12))->method('dispatch')->with(
            $this->stringContains('ongr_repository_crawler'),
            $this->anything()
        );
        // Explanation: 12 = ((start, source, modify, consume, finish) + (crawlerChunkEvent))*2.

        $pipeline = $this->getContainer()->get('ongr_connections.pipeline_factory');
        $pipeline->setDispatcher($dispatcher);
        $crawler = $this->getContainer()->get('ongr.repository_crawler.crawler');
        $crawler->setPipelineChunk($pipeline);
        $crawler->addContext('test_context', $context);
        $crawler->runAsync('test_context');
        $crawler->runAsync('test_context', '1');
    }

    /**
     * Test for runAsync() in case context is not set.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Context with name 'test_context' does not exist.
     */
    public function testRunAsyncExceptionPipeline()
    {
        $crawler = new Crawler();
        $crawler->runAsync('test_context');
    }
}
