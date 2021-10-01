<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Pimcore\Extension\Document\Areabrick\AreabrickInterface;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AreaBrickAutoloadWatcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $config = $container->getParameter('pimcore.config');

        if (!$config['documents']['areas']['autoload']) {
            return;
        }

        $possibleNoPimcoreAwareBricks = [];

        foreach ($container->getDefinitions() as $definitionId => $definition) {
            if (!str_contains((string) $definitionId, '.area.brick.')) {
                continue;
            }

            if (count(array_filter($definition->getTags(), static function ($tag) {
                return in_array($tag, ['toolbox.area.brick', 'toolbox.area.simple_brick']);
            }, ARRAY_FILTER_USE_KEY)) !== 0) {
                continue;
            }

            $class = $definition->getClass();
            if (empty($definition->getClass()) && $definition instanceof ChildDefinition) {
                $class = $definition->getParent();
            }

            if (empty($class)) {
                continue;
            }

            $reflector = new \ReflectionClass($class);
            if (!$reflector->implementsInterface(AreabrickInterface::class)) {
                continue;
            }

            $possibleNoPimcoreAwareBricks[] = $definitionId;
        }

        if (count($possibleNoPimcoreAwareBricks) === 0) {
            return;
        }

        throw new InvalidDefinitionException(sprintf(
            'Following classes have been auto-registered by PIMCORE which is not allowed when using the Toolbox Bundle.' .
            'Please disable the area autoload feature (pimcore.documents.areas.autoload = false), remove the classes or register them by using the toolbox.area.brick tag: %s',
            implode(', ', $possibleNoPimcoreAwareBricks)
        ));
    }
}
