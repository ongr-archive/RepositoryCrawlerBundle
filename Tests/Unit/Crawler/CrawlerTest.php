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

use ONGR\ConnectionsBundle\Pipeline\Event\ItemPipelineEvent;
use ONGR\ConnectionsBundle\Pipeline\Event\SourcePipelineEvent;
use ONGR\ConnectionsBundle\Pipeline\PipelineFactory;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\RepositoryCrawlerBundle\Event\CrawlerConsumer;

class CrawlerTest extends ElasticsearchTestCase
{
    /**
     * Test for run().
     */
    public function testRun()
    {
        $crawler = $this->getContainer()->get('ongr.repository_crawler.crawler');
        // Temporary workaround for ESB issue #34 (https://github.com/ongr-io/ElasticsearchBundle/issues/34).
        usleep(50000);
        $crawler->run();
    }

    /**
     * Test if CrawlerSource throws an exception when given wrong pipeline context.
     *
     * @expectedException \LogicException
     */
    public function testSourceContextCheck()
    {
        $source = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Event\AbstractCrawlerSource');

        $sourceEvent = new SourcePipelineEvent();

        $context = 'Obviously wrong context!';

        $sourceEvent->setContext($context);

        $source->registerSource($sourceEvent, []);
    }

    /**
     * Test if CrawlerConsumer throws an exception when given wrong pipeline context.
     *
     * @expectedException \LogicException
     */
    public function testConsumerContextCheck()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');

        $consumer = new CrawlerConsumer();

        $itemEvent = new ItemPipelineEvent($document1);

        $context = 'Obviously wrong context!';

        $itemEvent->setContext($context);

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher
            ->expects($this->any())
            ->method('dispatch')
            ->withConsecutive(
                ['ongr.pipeline.repository_crawler.default.source', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.start', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.finish', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.modify', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.consume', $this->anything()]
            )
            ->willReturnOnConsecutiveCalls(
                ($this->returnValue(null)),
                ($this->returnValue(null)),
                ($this->returnValue(null)),
                ($this->returnValue(null)),
                ($this->returnValue($consumer->onConsume($itemEvent) === null))
            );

        $pipelineFactory = new PipelineFactory();

        $pipelineFactory->setClassName('\ONGR\ConnectionsBundle\Pipeline\Pipeline');
        $pipelineFactory->setDispatcher($dispatcher);

        $crawler = new Crawler();
        $crawler->setPipelineFactory($pipelineFactory);
        $crawler->run();
    }


}
