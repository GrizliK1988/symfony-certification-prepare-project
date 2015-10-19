<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.10.15
 * Time: 8:34
 */

namespace DG\SymfonyCert\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class SecurityConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('security');

        $root
            ->children()
                ->arrayNode('firewalls')
                    ->children()
                        ->arrayNode('api_auth_by_key')
                            ->children()
                                ->arrayNode('keys')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('key')->end()
                                            ->scalarNode('name')->end()
                                            ->arrayNode('roles')
                                                ->defaultValue([])
                                                ->prototype('scalar')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('dao_auth')
                            ->children()
                                ->arrayNode('users')
                                    ->useAttributeAsKey('username')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('username')->end()
                                            ->scalarNode('password')->end()
                                            ->arrayNode('roles')
                                                ->defaultValue([])
                                                ->prototype('scalar')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
