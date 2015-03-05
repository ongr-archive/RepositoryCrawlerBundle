<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Functional\Command;

use ONGR\RepositoryCrawlerBundle\Command\RepositoryCrawlerCommand;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\ConnectionsBundle\Tests\Model\ProductModel;
use ONGR\RepositoryCrawlerBundle\Tests\Fixtures\TestDocumentProcessor;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Integration test for ongr:repository-crawler:crawl command.
 */
class RepositoryCrawlerCommandTest extends ElasticsearchTestCase
{
    /**
     * Creates and returns ProductModel array filled with test data.
     *
     * @param Repository $repository
     *
     * @return array|ProductModel
     */
    protected function getDocumentsData($repository)
    {
        $document = $repository->createDocument();

        $document->setId('test-product-1');
        $document->title = 'Test title';
        $document->setScore(1.0);

        $this->getManager()->persist($document);

        $document2 = $repository->createDocument();
        $document2->setId('test-product-2');
        $document2->title = 'Test title2';
        $document2->setScore(1.0);

        $this->getManager()->persist($document2);
        $this->getManager()->commit();

        $return[$document->getId()] = $document;
        $return[$document2->getId()] = $document2;

        return $return;
    }

    /**
     * Check if all documents are passed to the crawler context from DB.
     */
    public function testExecute()
    {
        $kernel = self::createClient()->getKernel();

        /** @var Repository $repository */
        $repository = $this->getManager()->getRepository('ONGRTestingBundle:Product');

        $expectedProducts = $this->getDocumentsData($repository);

        $consumer = new TestDocumentProcessor();
        $kernel->getContainer()->set('ongr.pipeline.repository_crawler.crawler_process_document', $consumer);

        $application = new Application($kernel);
        $application->add(new RepositoryCrawlerCommand());
        $command = $application->find('ongr:repository-crawler:crawl');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        foreach ($consumer->documentCollection as $item) {
            $this->assertEquals($expectedProducts[$item->getId()]->getTitle(), $item->getTitle());
            $this->assertEquals($expectedProducts[$item->getId()]->getScore(), $item->getScore());
            $this->assertEquals($expectedProducts[$item->getId()]->getId(), $item->getId());
        }
    }
}
