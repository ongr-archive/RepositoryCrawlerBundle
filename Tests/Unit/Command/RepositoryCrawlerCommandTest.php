<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepoositoryCrawlerBundle\Tests\Unit\Command;

use ONGR\ConnectionsBundle\Pipeline\Event\ItemPipelineEvent;
use ONGR\ConnectionsBundle\Pipeline\Event\SourcePipelineEvent;
use ONGR\ConnectionsBundle\Pipeline\PipelineFactory;
use ONGR\RepositoryCrawlerBundle\Command\RepositoryCrawlerCommand;
use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\RepositoryCrawlerBundle\Event\CrawlerConsumer;
use ONGR\RepositoryCrawlerBundle\Event\CrawlerPipelineContext;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RepositoryCrawlerCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test ongr:repository-crawler:crawl behaviour.
     */
    public function testCommand()
    {
        /** @var Crawler|\PHPUnit_Framework_MockObject_MockObject $crawler */
        $crawler = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\Crawler')
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();

        $crawler->expects($this->once())->method('run')->will($this->returnValue(null));

        $container = new ContainerBuilder();
        $container->set('ongr.repository_crawler.crawler', $crawler);

        $command = new RepositoryCrawlerCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $commandForTesting = $application->find('ongr:repository-crawler:crawl');
        $commandTester = new CommandTester($commandForTesting);

        $commandTester->execute(
            [
                'command' => $commandForTesting->getName(),
            ]
        );
    }

    /**
     * Test ongr:repository-crawler:crawl progress helper behavior.
     */
    public function testCommandProgress()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');

        $source = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Event\AbstractCrawlerSource');

        $modifier = $this->getMockForAbstractClass('ONGR\RepositoryCrawlerBundle\Event\AbstractCrawlerModifier');

        $container = new ContainerBuilder();

        $consumer = new CrawlerConsumer();

        $command = new RepositoryCrawlerCommand();
        $command->setContainer($container);

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $input->expects($this->once())->method('getOption')->with('event-name')->will(
            $this->returnValue('default')
        );

        $writes = 0;
        $callback = function () use (&$writes) {
            $writes++;
        };

        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputFormatter = $this->getMock('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $output->expects($this->any())->method('write')->will($this->returnCallback($callback));
        $output->expects($this->any())->method('isDecorated')->will($this->returnValue(true));
        $output->expects($this->any())->method('getFormatter')->will($this->returnValue($outputFormatter));

        $itemEvent = new ItemPipelineEvent($document1);
        $context = new CrawlerPipelineContext($output);
        $itemEvent->setContext($context);

        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $dispatcher
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                ['ongr.pipeline.repository_crawler.default.source', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.start', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.finish', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.modify', $this->anything()],
                ['ongr.pipeline.repository_crawler.default.consume', $this->anything()]
            )
            ->willReturnOnConsecutiveCalls(
                ($this->returnValue($source->onSource(new SourcePipelineEvent()))),
                ($this->returnValue(null)),
                ($this->returnValue(null)),
                ($this->returnValue($modifier->onModify($itemEvent))),
                ($this->returnValue($consumer->onConsume($itemEvent) === null))
            );

        $pipelineFactory = new PipelineFactory();

        $pipelineFactory->setClassName('\ONGR\ConnectionsBundle\Pipeline\Pipeline');
        $pipelineFactory->setDispatcher($dispatcher);

        $crawler = new Crawler();
        $crawler->setPipelineFactory($pipelineFactory);
        $crawler->setOutput($output);

        $container->set('ongr.repository_crawler.crawler', $crawler);

        $command->run($input, $output);

        if ($writes < 2) {
            $this->fail(
                "Console output was expected to be written at least 2 times, actually written {$writes} times."
            );
        }
    }
}
