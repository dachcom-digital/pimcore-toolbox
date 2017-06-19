<?php

namespace ToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('toolbox');

        $allowedTypes = [
            'select',
            'additionalClasses',
            'checkbox',
            'input',
            'numeric',
            'href',
            'multihref',
            'parallaximage',
        ];

        $rootNode
            ->children()
                ->arrayNode('areas')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('configElements')
                                ->useAttributeAsKey('name')
                                ->prototype('array')

                                    ->children()
                                        ->enumNode('type')->isRequired()->values($allowedTypes)->end()
                                        ->variableNode('config')->end()
                                    ->end()
                                    ->beforeNormalization()->castToArray()->end()
                                    ->validate()
                                        ->ifTrue(function($v) { return $v['enabled'] === FALSE; })
                                        ->thenUnset()
                                    ->end()
                                    ->canBeUnset()
                                    ->canBeDisabled()
                                    ->treatNullLike(['enabled' => FALSE])

                                ->end()
                            ->end()
                            ->variableNode('configParameter')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('custom_areas')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('configElements')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->enumNode('type')->isRequired()->values($allowedTypes)->end()
                                        ->variableNode('config')->isRequired()->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function($v) { return $v['enabled'] === FALSE; })
                                        ->thenUnset()
                                    ->end()
                                    ->canBeUnset()
                                    ->canBeDisabled()
                                    ->treatNullLike(['enabled' => FALSE])
                                    ->end()
                                ->end()
                            ->variableNode('configParameter')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('ckeditor')
                    ->children()
                        ->variableNode('config')->isRequired()->end()
                        ->variableNode('globalStyleSets')->isRequired()->end()
                        ->arrayNode('areaEditor')
                            ->children()
                                ->variableNode('config')
                                    ->validate()->ifEmpty()->thenUnset()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('objectEditor')
                            ->children()
                                ->variableNode('config')
                                    ->validate()->ifEmpty()->thenUnset()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('disallowedSubAreas')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('disallowed')
                                ->isRequired()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->variableNode('disallowedContentSnippetAreas')->end()
                ->arrayNode('areaBlockConfiguration')
                    ->children()
                        ->arrayNode('toolbar')
                            ->children()
                                ->scalarNode('title')->end()
                                ->integerNode('width')->end()
                                ->integerNode('x')->end()
                                ->integerNode('y')->end()
                                ->scalarNode('xAlign')->end()
                                ->scalarNode('xAlign')->end()
                                ->integerNode('buttonWidth')->end()
                                ->integerNode('buttonMaxCharacters')->end()
                            ->end()
                        ->end()
                        ->variableNode('groups')->end()
                    ->end()
                ->end()
                ->booleanNode('enableAssetHandler')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}