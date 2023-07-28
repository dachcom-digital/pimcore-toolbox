<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Pimcore\Document\Editable\EditableHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class EditableHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter('toolbox.headless_aware')) {
            return;
        }

        $container->getDefinition(EditableHandler::class)->setClass(\ToolboxBundle\Document\Editable\EditableHandler::class);
    }
}
