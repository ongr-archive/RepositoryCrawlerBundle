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

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\RepositoryCrawlerBundle\Event\CrawlerConsumer;

class CrawlerTest extends ElasticsearchTestCase
{
    /**
     * Test for run().
     */
    public function testRun()
    {
        $crawler = $this->getContainer()->get('ongr_repository_crawler.crawler');
        // Temporary workaround for ESB issue #34 (https://github.com/ongr-io/ElasticsearchBundle/issues/34).
        usleep(50000);

        $writes = 0;
        $callback = function () use (&$writes) {
            $writes++;
        };
        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $outputFormatter = $this->getMock('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $output->expects($this->any())->method('write')->will($this->returnCallback($callback));
        $output->expects($this->any())->method('isDecorated')->will($this->returnValue(true));
        $output->expects($this->any())->method('getFormatter')->will($this->returnValue($outputFormatter));

        $crawler->setOutput($output);
        $crawler->startCrawler('repository_crawler.');

        $this->assertInstanceOf('ONGR\RepositoryCrawlerBundle\Crawler\Crawler', $crawler);
    }
}
