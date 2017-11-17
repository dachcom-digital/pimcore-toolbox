<?php

namespace ToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator;
use ToolboxBundle\Calculator\Bootstrap4\SlideColumnCalculator;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('toolbox');

        $coreTypes = [
            'areablock',
            'area',
            'block',
            'checkbox',
            'date',
            'href',
            'image',
            'input',
            'link',
            'multihref', // has default
            'multiselect', // has default
            'numeric',
            'embed',
            'pdf',
            'renderlet',
            'select',
            'snippet',
            'table',
            'textarea',
            'video',
            'wysiwyg'
        ];

        $customTypes = [
            'additionalClasses',
            'parallaximage',
            'googlemap',
            'vhs',
            'dynamiclink',

        ];

        //@todo: get them dynamically!!
        $allowedTypes = array_merge($coreTypes, $customTypes);

        $rootNode
            ->children()
                ->arrayNode('flags')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('strict_column_counter')->defaultValue(FALSE)->end()
                    ->end()
                ->end()
                ->arrayNode('areas')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('config_elements')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->enumNode('type')->isRequired()->values($allowedTypes)->end()
                                        ->scalarNode('title')->defaultValue(NULL)->end()
                                        ->scalarNode('description')->defaultValue(NULL)->end()
                                        ->scalarNode('col_class')->defaultValue(NULL)->end()
                                        ->variableNode('conditions')->defaultValue(NULL)->end()
                                        ->variableNode('config')->defaultValue([])->end()
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
                            ->variableNode('config_parameter')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('custom_areas')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('config_elements')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->enumNode('type')->isRequired()->values($allowedTypes)->end()
                                        ->scalarNode('title')->defaultValue(NULL)->end()
                                        ->scalarNode('description')->defaultValue(NULL)->end()
                                        ->scalarNode('col_class')->defaultValue(NULL)->end()
                                        ->variableNode('conditions')->defaultValue(NULL)->end()
                                        ->variableNode('config')->defaultValue([])->end()
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
                            ->variableNode('config_parameter')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ckeditor')
                    ->children()
                        ->variableNode('config')->isRequired()->end()
                        ->variableNode('global_style_sets')->isRequired()->end()
                        ->arrayNode('area_editor')
                            ->children()
                                ->variableNode('config')
                                    ->validate()->ifEmpty()->thenUnset()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('object_editor')
                            ->children()
                                ->variableNode('config')
                                    ->validate()->ifEmpty()->thenUnset()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('image_thumbnails')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('disallowed_subareas')
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
                ->variableNode('disallowed_content_snippet_areas')->end()
                ->arrayNode('area_block_configuration')
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
                ->arrayNode('theme')
                    ->children()
                        ->scalarNode('layout')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('default_layout')
                            ->defaultValue(false)
                        ->end()
                        ->arrayNode('calculators')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('ToolboxBundle\Calculator\ColumnCalculator')->defaultValue(ColumnCalculator::class)->end()
                                ->scalarNode('ToolboxBundle\Calculator\SlideColumnCalculator')->defaultValue(SlideColumnCalculator::class)->end()
                            ->end()
                        ->end()
                        ->arrayNode('grid')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('grid_size')->min(0)->defaultValue(12)->end()
                                ->arrayNode('breakpoints')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('identifier')->isRequired()->end()
                                            ->scalarNode('name')->defaultValue(NULL)->end()
                                            ->scalarNode('description')->defaultValue(NULL)->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('wrapper')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                            ->performNoDeepMerging()
                            ->beforeNormalization()
                                ->ifTrue(function ($v) { return is_array($v) && !isset($v['wrapper_classes']); })
                                ->then(function ($v) { return array('wrapper_classes' => $v); })
                            ->end()
                                ->children()
                                    ->arrayNode('wrapper_classes')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('tag')->end()
                                                ->scalarNode('class')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('data_attributes')
                    ->useAttributeAsKey('name')
                        ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function ($v) { return is_array($v) && !isset($v['values']); })
                            ->then(function ($v) { return array('values' => $v); })
                        ->end()
                        ->children()
                            ->variableNode('values')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}