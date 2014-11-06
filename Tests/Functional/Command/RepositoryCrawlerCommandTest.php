<?php

namespace Fox\ConnectionsBundle\Tests\Integration\Command;

use ONGR\RepositoryCrawlerBundle\Command\RepositoryCrawlerCommand;
use ONGR\RepositoryCrawlerBundle\Crawler;
use ONGR\RepositoryCrawlerBundle\CrawlerContextInterface;
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\ConnectionsBundle\Tests\Model\ProductModel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Integration test for fox:repository:crawler command
 */
class RepositoryCrawlerCommandTest extends ElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDocumentsData()
    {
        return [
            'ProductModel' => [
                [
                    '_id' => 'test-product-1',
                    'id' => 'test-product-1',
                    'parentId' => '',
                    'sku' => 'P00001',
                    'title' => 'Test title'
                ],
                [
                    '_id' => 'test-product-2',
                    'id' => 'test-product-2',
                    'parentId' => '',
                    'sku' => 'P00002',
                    'title' => 'Test titl2'
                ],
            ],
        ];
    }

    /**
     * Check if all documents are passed to the crawler context from DB
     */
    public function testExecute()
    {
        $kernel = self::createClient()->getKernel();
        $container = $kernel->getContainer();

        $product1 = new ProductModel();
        $product1->setScore(0.0);

        $product1->assign($this->getDocumentsData()['ProductModel'][0]);

        $product2 = new ProductModel();
        $product2->setDocumentScore(0.0);
        $product2->assign($this->getDocumentsData()['ProductModel'][1]);
        $expectedProducts = [$product1, $product2];
        $actualProducts = [];

        /** @var Repository $repository */
        $repository = $this->getManager()->getRepository('AcmeTestBundle:Product');

        /** @var CrawlerContextInterface|\PHPUnit_Framework_MockObject_MockObject $dummyContext */
        $dummyContext = $this->getMockBuilder('ONGR\RepositoryCrawlerBundle\Crawler\CrawlerContextInterface')
            ->getMock();
        $dummyContext->expects($this->once())->method('getRepository')->willReturn($repository);
        $dummyContext->expects($this->once())->method('getSearch')->willReturn(new Search());
        $dummyContext->expects($this->exactly(count($expectedProducts)))->method('processData')->willReturnCallback(
            function (DocumentInterface $baseModel) use (&$actualProducts) {
                $actualProducts[] = $baseModel;
            }
        );

        /** @var Crawler $repositoryCrawler */
        $repositoryCrawler = $this->getContainer()->get('ongr.repository_crawler');
        $repositoryCrawler->addContext('test', $dummyContext);

        $application = new Application($kernel);
        $application->add(new RepositoryCrawlerCommand());
        $command = $application->find('ongr:repository-crawler');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'context' => 'test',
            ]
        );

        sort($expectedProducts);
        sort($actualProducts);

        $this->assertEquals($expectedProducts, $actualProducts);
    }
}
