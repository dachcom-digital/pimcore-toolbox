<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Registry\NormalizerRegistry;

final class NormalizerRegistryPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(NormalizerRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('toolbox.property.normalizer', true);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('register', [$id, new Reference($id)]);
        }
    }
}
