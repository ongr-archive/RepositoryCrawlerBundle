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

use ONGR\RepositoryCrawlerBundle\Command\RepositoryCrawlerCommand;
use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\RepositoryCrawlerBundle\Tests\Utils\ResultsIteratorBuilder;
use ONGR\ElasticsearchBundle\DSL\Search;
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
        $contextName = 'test_context';

        /** @var Crawler|\PHPUnit_Framework_MockObject_MockObject $crawler */
        $crawler = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\Crawler')
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();

        $crawler->expects($this->once())->method('run')->with($contextName);

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
                'context' => $contextName,
            ]
        );
    }

    /**
     * Test ongr:repository-crawler:crawl behavior in case of asynchronous processing.
     */
    public function testCommandAsync()
    {
        $contextName = 'test_context';
        $scrollId = 'test_scroll_id';

        $container = new ContainerBuilder();

        $command = new RepositoryCrawlerCommand();
        $command->setContainer($container);

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');

        $input->expects($this->once())->method('getArgument')->with('context')->will(
            $this->returnValue($contextName)
        );

        $input->expects($this->any())->method('getOption')->will(
            $this->returnCallback(
                function ($argument) use ($scrollId) {
                    if ($argument === 'async') {
                        return true;
                    }

                    return $argument === 'scroll-id' ? $scrollId : null;
                }
            )
        );

        $crawler = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\Crawler')
            ->disableOriginalConstructor()
            ->setMethods(['runAsync'])
            ->getMock();

        $crawler->expects($this->once())->method('runAsync')->with($contextName, $scrollId);

        $container->set('ongr.repository_crawler.crawler', $crawler);

        $command->run($input, $output);
    }

    /**
     * Test ongr:repository-crawler:crawl progress helper behavior.
     */
    public function testCommandProgress()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document2 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');

        $iterator = ResultsIteratorBuilder::getMock($this, [$document1, $document2]);

        $repository = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())->method('execute')->will($this->returnValue($iterator));

        $context = $this->getMock('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface');
        $context->expects($this->once())->method('getRepository')->will($this->returnValue($repository));
        $context->expects($this->once())->method('getSearch')->will($this->returnValue(new Search()));

        $contextName = 'test_context';
        $container = new ContainerBuilder();

        $command = new RepositoryCrawlerCommand();
        $command->setContainer($container);

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $input->expects($this->once())->method('getArgument')->with('context')->will(
            $this->returnValue($contextName)
        );
        $input->expects($this->any())->method('getOption')->will($this->returnValue(null));

        $writes = 0;
        $callback = function () use (&$writes) {
            $writes++;
        };

        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputFormatter = $this->getMock('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $output->expects($this->any())->method('write')->will($this->returnCallback($callback));
        $output->expects($this->any())->method('isDecorated')->will($this->returnValue(true));
        $output->expects($this->any())->method('getFormatter')->will($this->returnValue($outputFormatter));

        $crawler = new Crawler();
        $crawler->addContext($contextName, $context);
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
