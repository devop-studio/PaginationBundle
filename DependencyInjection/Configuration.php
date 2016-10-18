<?php

namespace Millennium\PaginationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root('millennium_pagination')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('options')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('page')->defaultValue('page')->end()
                                ->scalarNode('limit')->defaultValue(10)->end()
                                ->scalarNode('offset')->defaultValue(2)->end()
                                ->booleanNode('show_empty')->defaultTrue()->end()
                            ->end()
                        ->end()
                        ->arrayNode('classes')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('nav')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['text-center'])
                                ->end()
                                ->arrayNode('ul')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['pagination', 'pagination-sm'])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('template')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('pagination')
                                ->defaultValue('MillenniumPaginationBundle:Pagination:pagination.html.twig')
                                ->end()
                            ->end()
                        ->end()
                    ->end();

        return $builder;
    }
}
