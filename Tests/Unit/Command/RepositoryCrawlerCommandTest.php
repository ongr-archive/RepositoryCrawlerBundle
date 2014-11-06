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

namespace Fox\ConnectionsBundle\Tests\Functional\Command;

use ONGR\RepositoryCrawlerBundle\Command\RepositoryCrawlerCommand;
use ONGR\RepositoryCrawlerBundle\Crawler;
use ONGR\RepositoryCrawlerBundle\Tests\Utils\ResultsIteratorBuilder;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RepositoryCrawlerCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test fox:sync:repository-crawler behavior
     */
    public function testCommand()
    {
        $contextName = 'test_context';

        /** @var Crawler|\PHPUnit_Framework_MockObject_MockObject $crawler */
        $crawler = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler')
            ->disableOriginalConstructor()
            ->setMethods(array('run'))
            ->getMock();

        $crawler->expects($this->once())->method('run')->with($contextName);

        $container = new ContainerBuilder();
        $container->set('ongr.repository_crawler', $crawler);

        $command = new RepositoryCrawlerCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $commandForTesting = $application->find('ongr:repository-crawler');
        $commandTester = new CommandTester($commandForTesting);

        $commandTester->execute(
            [
                'command'     => $commandForTesting->getName(),
                'context'     => $contextName
            ]
        );
    }

    /**
     * Test fox:sync:repository-crawler behavior in case of asynchronous processing
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

        $crawler = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler')
            ->disableOriginalConstructor()
            ->setMethods(array('runAsync'))
            ->getMock();

        $crawler->expects($this->once())->method('runAsync')->with($contextName, $scrollId);

        $container->set('ongr.repository_crawler', $crawler);

        $command->run($input, $output);
    }

    /**
     * Test fox:sync:repository-crawler progress helper behavior
     */
    public function testCommandProgress()
    {
        $document1 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document2 = $this->getMockForAbstractClass('ONGR\ElasticsearchBundle\Document\DocumentInterface');

        $iterator = ResultsIteratorBuilder::getMock($this, array($document1, $document2));

        $repository = $this->getMock('ONGR\ElasticsearchBundle\ORM\Repository');
        $repository->expects($this->once())->method('exportDocuments')->will($this->returnValue($iterator));

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
        $output->expects($this->any())->method('write')->will($this->returnCallback($callback));
        $output->expects($this->any())->method('isDecorated')->will($this->returnValue(true));

        $crawler = new Crawler();
        $crawler->addContext($contextName, $context);
        $crawler->setOutput($output);

        $container->set('ongr.repository_crawler', $crawler);

        $command->run($input, $output);

        if ($writes < 2) {
            $this->fail(
                "Console output was expected to be written at least 2 times, actually written {$writes} times."
            );
        }
    }
}
