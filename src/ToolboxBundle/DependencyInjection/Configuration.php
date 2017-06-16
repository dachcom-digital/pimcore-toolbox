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
                ->variableNode('disallowedSubAreas')->end()
                ->variableNode('disallowedContentSnippetAreas')->end()
                ->variableNode('areaBlockConfiguration')->end()
                ->booleanNode('enableAssetHandler')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}