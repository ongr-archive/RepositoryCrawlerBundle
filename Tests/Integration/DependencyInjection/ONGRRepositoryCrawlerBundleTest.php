<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\RepositoryCrawlerBundle\Tests\Integration\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ONGRRepositoryCrawlerBundleTest extends WebTestCase
{
    /**
     * Tests DependencyInjection Bundle.
     */
    public function testBundle()
    {
        $kernel = self::createClient()->getKernel();
        $this->assertInstanceOf(
            'ONGR\RepositoryCrawlerBundle\ONGRRepositoryCrawlerBundle',
            $kernel->getBundle('ONGRRepositoryCrawlerBundle')
        );
    }
}
