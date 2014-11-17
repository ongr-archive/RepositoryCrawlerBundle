<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace ONGR\RepositoryCrawlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects registered crawler event listeners and injects to crawler.
 */
class CrawlerListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('ongr.repository_crawler.crawler');

        $consumerIdList = [];
        $consumerServices = $container->findTaggedServiceIds('kernel.event_listener');

        foreach ($consumerServices as $id => $attributes) {
            $consumerIdList[] = $id;
        }

        $definition->addMethodCall('setConsumeEventListeners', [$consumerIdList]);
    }
}
