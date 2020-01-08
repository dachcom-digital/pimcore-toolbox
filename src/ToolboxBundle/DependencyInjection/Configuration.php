<?php

namespace ToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator;
use ToolboxBundle\Calculator\Bootstrap4\SlideColumnCalculator;
use ToolboxBundle\Resolver\ContextResolver;
use ToolboxBundle\ToolboxConfig;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('toolbox');

        $this->getConfigNode($rootNode);
        $this->addContextNode($rootNode);

        $rootNode
            ->children()
                ->scalarNode('context_resolver')->defaultValue(ContextResolver::class)->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    public function addContextNode(ArrayNodeDefinition $rootNode)
    {
        $node = $rootNode
            ->children()
                ->arrayNode('context')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('settings')
                                ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return $v['merge_with_root'] === false && (!empty($v['disabled_areas']));
                                    })
                                    ->then(function ($v) {
                                        @trigger_error('Toolbox context conflict: "merge_with_root" is disabled but there are defined elements in "disabled_areas"', E_USER_ERROR);

                                        return $v;
                                    })
                                ->end()
                                ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return $v['merge_with_root'] === false && (!empty($v['enabled_areas']));
                                    })
                                    ->then(function ($v) {
                                        @trigger_error('Toolbox context conflict: "merge_with_root" is disabled but there are defined elements in "enabled_areas"', E_USER_ERROR);

                                        return $v;
                                    })
                                ->end()
                                ->isRequired()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('merge_with_root')->defaultValue(true)->end()
                                    ->variableNode('disabled_areas')->defaultValue([])->end()
                                    ->variableNode('enabled_areas')->defaultValue([])->end()
                                ->end()
                            ->end()
                        ->end();

        $this->getConfigNode($node);

        $node->end()
            ->end()
        ->end();
    }

    /**
     * @param NodeDefinition $rootNode
     */
    public function getConfigNode(NodeDefinition $rootNode)
    {
        //@todo: get them dynamically!!
        $allowedTypes = array_merge(ToolboxConfig::CORE_TYPES, ToolboxConfig::CUSTOM_TYPES);

        $rootNode
            ->children()
                ->arrayNode('flags')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('strict_column_counter')->defaultValue(false)->end()
                        ->booleanNode('use_dynamic_links')->defaultValue(false)->end()
                    ->end()
                ->end()
                ->arrayNode('areas')
                    ->validate()
                        ->ifTrue(function ($v) {
                            return count(array_diff(array_keys($v), ToolboxConfig::TOOLBOX_TYPES)) > 0;
                        })
                        ->then(function ($v) {
                            $invalidTags = array_diff(array_keys($v), ToolboxConfig::TOOLBOX_TYPES);

                            throw new InvalidConfigurationException(sprintf(
                                'Invalid elements in toolbox "area" configuration: %s. to add custom areas, use the "custom_area" node. allowed tags for "area" are: %s',
                                implode(', ', $invalidTags),
                                implode(', ', ToolboxConfig::TOOLBOX_TYPES)
                            ));
                        })
                    ->end()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('config_elements')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->enumNode('type')->isRequired()->values($allowedTypes)->end()
                                        ->scalarNode('title')->defaultValue(null)->end()
                                        ->scalarNode('description')->defaultValue(null)->end()
                                        ->scalarNode('col_class')->defaultValue(null)->end()
                                        ->variableNode('conditions')->defaultValue(null)->end()
                                        ->variableNode('config')->defaultValue([])->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function ($v) {
                                            return $v['enabled'] === false;
                                        })
                                        ->thenUnset()
                                    ->end()
                                    ->canBeUnset()
                                    ->canBeDisabled()
                                    ->treatnullLike(['enabled' => false])

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
                                        ->scalarNode('type')->isRequired()->end()
                                        ->scalarNode('title')->defaultValue(null)->end()
                                        ->scalarNode('description')->defaultValue(null)->end()
                                        ->scalarNode('col_class')->defaultValue(null)->end()
                                        ->variableNode('conditions')->defaultValue(null)->end()
                                        ->variableNode('config')->defaultValue([])->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function ($v) {
                                            return $v['enabled'] === false;
                                        })
                                        ->thenUnset()
                                    ->end()
                                    ->canBeUnset()
                                    ->canBeDisabled()
                                    ->treatnullLike(['enabled' => false])
                                    ->end()
                                ->end()
                            ->variableNode('config_parameter')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ckeditor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->variableNode('config')->defaultValue([])->end()
                        ->variableNode('global_style_sets')->defaultValue([])->end()
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
                // @deprecated: remove with toolbox 4.0
                ->arrayNode('disallowed_subareas')
                    ->setDeprecated('The "%node%" option key is deprecated since version 2.3 and will be removed in Toolbox 4.0. Use the "areas_appearance" configuration key instead')
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
                ->arrayNode('areas_appearance')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('disallowed')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('allowed')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                // @deprecated: remove with toolbox 4.0
                ->variableNode('disallowed_content_snippet_areas')
                    ->setDeprecated('The "%node%" option is deprecated since version 2.3 and will be removed in Toolbox 4.0. Use the "snippet_areas_appearance" configuration key instead')
                ->end()
                ->arrayNode('snippet_areas_appearance')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('disallowed')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('allowed')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                                ->scalarNode('column_calculator')->defaultValue(ColumnCalculator::class)->end()
                                ->scalarNode('ToolboxBundle\Calculator\ColumnCalculator')->defaultValue(ColumnCalculator::class)
                                    ->setDeprecated('The "%node%" option is deprecated since version 2.3 and will be removed in Toolbox 4.0. Use "column_calculator" instead.')
                                ->end()
                                ->scalarNode('slide_calculator')->defaultValue(SlideColumnCalculator::class)->end()
                                ->scalarNode('ToolboxBundle\Calculator\SlideColumnCalculator')->defaultValue(SlideColumnCalculator::class)
                                    ->setDeprecated('The "%node%" option is deprecated since version 2.3 and will be removed in Toolbox 4.0. Use "slide_calculator" instead.')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('grid')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('grid_size')->min(0)->defaultValue(12)->end()
                                ->arrayNode('column_store')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('breakpoints')
                                    ->performNoDeepMerging()
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('identifier')->isRequired()->end()
                                            ->scalarNode('name')->defaultValue(null)->end()
                                            ->scalarNode('description')->defaultValue(null)->end()
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
                                ->ifTrue(function ($v) {
                                    return is_array($v) && !isset($v['wrapper_classes']);
                                })
                                ->then(function ($v) {
                                    return array('wrapper_classes' => $v);
                                })
                            ->end()
                                ->children()
                                    ->arrayNode('wrapper_classes')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('tag')->end()
                                                ->scalarNode('class')->end()
                                                ->scalarNode('attr')->end()
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
                            ->ifTrue(function ($v) {
                                return is_array($v) && !isset($v['values']);
                            })
                            ->then(function ($v) {
                                return array('values' => $v);
                            })
                        ->end()
                        ->children()
                            ->variableNode('values')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
