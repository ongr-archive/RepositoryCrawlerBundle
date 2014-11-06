<?php

namespace ONGR\RepositoryCrawlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges configuration from app/config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ongr_repositorycrawler');

        $rootNode
            ->children()
                ->arrayNode('sync')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('managers')
                            ->useAttributeAsKey('manager')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('manager')->end()
                                    ->scalarNode('job_manager')->end()
                                ->end()
                            ->end()
                            ->defaultValue(
                                [
                                    'default' => ['job_manager' => 'ongr_repositorycrawler.job_manager',]
                                ]
                            )
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('entity_namespace')
                    ->defaultValue('ONGRRepositoryCrawlerBundle:')
                    ->info('Namespace/alias for ONGRRepositoryCrawlerBundle related entities')
                    ->beforeNormalization()
                        ->ifTrue(
                            function ($value) {
                                return strpos($value, '\\') === false;
                            }
                        )
                        ->then(
                            function ($value) {
                                return rtrim($value, ':') . ':';
                            }
                        )
                    ->end()
                    ->beforeNormalization()
                        ->ifTrue(
                            function ($value) {
                                return strpos($value, '\\') !== false;
                            }
                        )
                        ->then(
                            function ($value) {
                                return rtrim($value, '\\') . '\\';
                            }
                        )
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
