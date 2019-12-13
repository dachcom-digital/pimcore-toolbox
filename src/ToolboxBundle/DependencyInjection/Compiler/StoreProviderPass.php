<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Registry\StoreProviderRegistry;

final class StoreProviderPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(StoreProviderRegistry::class);
        foreach ($container->findTaggedServiceIds('toolbox.editable.store_provider') as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('register', [$attributes['identifier'], new Reference($id)]);
            }
        }
    }
}
