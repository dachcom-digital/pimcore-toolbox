<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManagerInterface;
use ToolboxBundle\ToolboxConfig;

final class AreaBrickRegistryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // create abstract brick definition
        $abstractBrickDefinition = new Definition(AbstractAreabrick::class);
        $abstractBrickDefinition->setAbstract(true);

        $abstractBrickDefinition->addMethodCall('setLayoutManager', [new Reference(LayoutManagerInterface::class)]);
        $abstractBrickDefinition->addMethodCall('setConfigManager', [new Reference(ConfigManagerInterface::class)]);
        $abstractBrickDefinition->addMethodCall('setBrickConfigBuilder', [new Reference(BrickConfigBuilder::class)]);

        $container->setDefinition(AbstractAreabrick::class, $abstractBrickDefinition);

        // check for legacy naming
        $pimcoreTaggedServices = $container->findTaggedServiceIds('pimcore.area.brick', true);
        foreach ($pimcoreTaggedServices as $legacyId => $legacyTags) {

            $legacyBrickDefinition = $container->getDefinition($legacyId);

            if ($legacyBrickDefinition instanceof ChildDefinition && $legacyBrickDefinition->getParent() === AbstractAreabrick::class) {

                throw new InvalidDefinitionException(sprintf(
                    'Please use tag "%s" instead of "%s" to register your brick "%s" as a true toolbox area brick.',
                    'toolbox.area.brick',
                    'pimcore.area.brick',
                    $legacyId
                ));
            }
        }

        // register toolbox bricks
        $toolboxTaggedServices = $container->findTaggedServiceIds('toolbox.area.brick', true);
        foreach ($toolboxTaggedServices as $id => $tags) {
            $brickDefinition = $container->getDefinition($id);

            if (!$brickDefinition instanceof ChildDefinition) {
                throw new InvalidDefinitionException(sprintf(
                    'Areabrick "%s" needs to be a child of "%s"',
                    $id,
                    AbstractAreabrick::class
                ));
            }

            if ($brickDefinition->hasMethodCall('setAreaBrickType')) {
                throw new InvalidDefinitionException(sprintf(
                    'Please remove methodCall "%s" from your brick "%s". The type declaration will be processed internally.',
                    'setAreaBrickType',
                    $id
                ));
            }

            foreach ($tags as $attributes) {

                $type = AbstractAreabrick::AREABRICK_TYPE_EXTERNAL;

                if (str_starts_with($id, 'ToolboxBundle')) {
                    $type = AbstractAreabrick::AREABRICK_TYPE_INTERNAL;
                }

                if ($type === AbstractAreabrick::AREABRICK_TYPE_EXTERNAL && in_array($attributes['id'], ToolboxConfig::TOOLBOX_TYPES, true)) {
                    throw new InvalidDefinitionException(sprintf(
                        'ID "%s" for AreaBrick "%s is a reserved identifier. Please change the id of your custom AreaBrick. Internal IDs are: %s.',
                        $attributes['id'],
                        $id,
                        implode(', ', ToolboxConfig::TOOLBOX_TYPES)
                    ));
                }

                $brickDefinition->addTag('pimcore.area.brick', ['id' => $attributes['id']]);
                $brickDefinition->addMethodCall('setAreaBrickType', [$type]);
            }
        }
    }
}
