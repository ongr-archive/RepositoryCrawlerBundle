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
use ONGR\RepositoryCrawlerBundle\Tests\Utils\TestDocumentProcessor;
use ONGR\RepositoryCrawlerBundle\Tests\Utils\TestCrawlerContext;
use ONGR\RepositoryCrawlerBundle\Crawler\Crawler;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\ConnectionsBundle\Tests\Model\ProductModel;

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
        $document->setScore('1.0');

        $this->getManager()->persist($document);

        $document2 = $repository->createDocument();
        $document2->setId('test-product-2');
        $document2->title = 'Test title2';
        $document2->setScore('1.0');

        $this->getManager()->persist($document2);
        $this->getManager()->commit();


        return [$document, $document2];
    }

    /**
     * Check if all documents are passed to the crawler context from DB.
     */
    public function testExecute()
    {
        $kernel = self::createClient()->getKernel();

        /** @var Repository $repository */
        $repository = $this->getManager()->getRepository('AcmeTestBundle:Product');

        $expectedProducts = $this->getDocumentsData($repository);

        /** @var TestDocumentProcessor $processor */
        $processor = new TestDocumentProcessor();

        /** @var TestCrawlerContext $context */
        $context = new TestCrawlerContext($repository);
        $context->addDocumentProcessor($processor);

        /** @var Crawler $repositoryCrawler */
        $repositoryCrawler = $kernel->getContainer()->get('ongr.repository_crawler.crawler');
        $repositoryCrawler->addContext('test_context', $context);

        $application = new Application($kernel);
        $application->add(new RepositoryCrawlerCommand());
        $command = $application->find('ongr:repository-crawler:crawl');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'command' => $command->getName(),
                'context' => 'test_context',
            ]
        );

        sort($expectedProducts);
        if (is_array($processor->documentCollection)) {
            sort($processor->documentCollection);
        }


        $this->assertEquals($expectedProducts, $processor->documentCollection);
    }
}
