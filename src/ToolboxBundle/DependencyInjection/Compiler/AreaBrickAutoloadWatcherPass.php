<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class AreaBrickAutoloadWatcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $config = $container->getParameter('pimcore.config');

        if (!$config['documents']['areas']['autoload']) {
            return;
        }

        $possibleNoPimcoreAwareBricks = array_filter($container->getDefinitions(), function ($definitionId) {
            return str_contains((string) $definitionId, '.area.brick.');
        }, ARRAY_FILTER_USE_KEY);

        if (count($possibleNoPimcoreAwareBricks) === 0) {
            return;
        }

        throw new InvalidDefinitionException(sprintf(
            'Following classes have been auto-registered by PIMCORE which is not allowed when using the Toolbox Bundle.' .
            'Please disable the area autoload feature (pimcore.documents.areas.autoload = false), remove the classes or register them by using the toolbox.area.brick tag: %s',
            implode(', ', array_map(function (Definition $definition) {
                return $definition->getClass();
            }, $possibleNoPimcoreAwareBricks))
        ));

    }
}
