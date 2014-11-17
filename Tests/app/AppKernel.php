<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
            new Fox\UtilsBundle\FoxUtilsBundle(),
            new ONGR\ConnectionsBundle\ONGRConnectionsBundle(),
            new ONGR\RepositoryCrawlerBundle\ONGRRepositoryCrawlerBundle(),
            new ONGR\ElasticsearchBundle\Tests\app\fixture\Acme\TestBundle\AcmeTestBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
