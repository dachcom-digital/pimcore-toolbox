<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Registry\CalculatorRegistry;

final class CalculatorRegistryPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /* @deprecated since 2.3. gets removed in 4.0 */
        if ($container->hasParameter('toolbox.deprecation.calculator_tags')) {
            $preTaggedServices = $container->findTaggedServiceIds('toolbox.calculator', true);
            $deprecationTags = $container->getParameter('toolbox.deprecation.calculator_tags');
            foreach ($deprecationTags as $tag) {
                if (!in_array($tag[0], array_keys($preTaggedServices)) && $container->hasDefinition($tag[0])) {
                    $definition = $container->getDefinition($tag[0]);
                    $definition->addTag('toolbox.calculator', ['type' => $tag[1]]);
                }
            }
        }

        $taggedServices = $container->findTaggedServiceIds('toolbox.calculator', true);
        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition(CalculatorRegistry::class);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('register', [$id, new Reference($id), $attributes['type']]);
            }
        }
    }
}
