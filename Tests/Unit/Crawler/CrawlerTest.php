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

class CrawlerTest extends ElasticsearchTestCase
{
    /**
     * Test for run().
     */
    public function testRun()
    {
        $crawler = $this->getContainer()->get('ongr.repository_crawler.crawler');
        // Temporary workaround for ESB issue #34 (https://github.com/ongr-io/ElasticsearchBundle/issues/34)
        usleep(25000);
        $crawler->run();
    }
}
