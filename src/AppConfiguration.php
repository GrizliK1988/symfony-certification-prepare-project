<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 18.09.15
 * Time: 20:36
 */

namespace DG\SymfonyCert;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AppConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('edmunds_api');

        $rootNode->children()
            ->booleanNode('debug')
                ->defaultTrue()
                ->info('Enable debug mode')
            ->end()
            ->scalarNode('key')
                ->isRequired()
                ->cannotBeOverwritten()
            ->end()
            ->scalarNode('secret')
                ->isRequired()
                ->validate()
                ->ifTrue(function($v) { return $v == 'aaa'; })
                    ->thenInvalid('Invalid secret aaa')
                ->end()
            ->end()
            ->arrayNode('settings')
                ->children()
                    ->arrayNode('cache')
                        ->children()
                            ->arrayNode('makes')
                                ->fixXmlConfig('restrictedName')
                                ->canBeEnabled()
                                ->children()
                                    ->integerNode('count')
                                        ->isRequired()
                                        ->min(10)
                                        ->max(1000)
                                    ->end()
                                    ->arrayNode('restrictedNames')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('ratings_reviews')
                                ->performNoDeepMerging()
                                ->canBeEnabled()
                                ->children()
                                    ->integerNode('count')
                                        ->isRequired()
                                        ->min(10)
                                        ->max(1000)
                                    ->end()
                                    ->enumNode('sortby')
                                        ->treatNullLike('sortby')
                                        ->treatNullLike(false)
                                        ->treatNullLike(true)
                                        ->values(['created', 'thumbsUp', 'avgRating'])
                                    ->end()
                                    ->floatNode('minAvgRating')
                                        ->defaultValue(0)
                                    ->end()
                                    ->floatNode('maxAvgRating')
                                        ->defaultValue(5)
                                    ->end()
                                    ->append($this->addRatingFilters())
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }

    private function addRatingFilters()
    {
        $tree = new TreeBuilder();

        $node = $tree->root('filters');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('type')
            ->prototype('array')
                ->children()
                    ->integerNode('min')->defaultValue(0)->end()
                    ->integerNode('max')->defaultValue(5)->end()
                ->end()
            ->end();

        return $node;
    }
}