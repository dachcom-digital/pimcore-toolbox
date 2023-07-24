<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Builder\BrickConfigBuilderInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\AbstractBaseAreabrick;
use ToolboxBundle\Document\SimpleAreabrick\SimpleAreaBrick;
use ToolboxBundle\Document\SimpleAreabrick\SimpleAreaBrickConfigurable;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManagerInterface;
use ToolboxBundle\ToolboxConfig;

final class AreaBrickRegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $notEditDialogAwareBricks = [];

        // create abstract base brick definition
        $abstractBrickDefinition = new Definition(AbstractBaseAreabrick::class);
        $abstractBrickDefinition->setAbstract(true);
        $abstractBrickDefinition->addMethodCall('setLayoutManager', [new Reference(LayoutManagerInterface::class)]);
        $abstractBrickDefinition->addMethodCall('setConfigManager', [new Reference(ConfigManagerInterface::class)]);

        $container->setDefinition(AbstractBaseAreabrick::class, $abstractBrickDefinition);

        // create abstract brick definition
        $abstractConfigurableBrickDefinition = new ChildDefinition(AbstractBaseAreabrick::class);
        $abstractConfigurableBrickDefinition->setClass(AbstractAreabrick::class);
        $abstractConfigurableBrickDefinition->setAbstract(true);
        $abstractConfigurableBrickDefinition->addMethodCall('setBrickConfigBuilder', [new Reference(BrickConfigBuilderInterface::class)]);

        $container->setDefinition(AbstractAreabrick::class, $abstractConfigurableBrickDefinition);

        $additionalAreaBricksConfig = [];

        // check for legacy naming
        $pimcoreTaggedAreaBricksServices = $container->findTaggedServiceIds('pimcore.area.brick', true);
        foreach ($pimcoreTaggedAreaBricksServices as $legacyId => $legacyTags) {
            $legacyBrickDefinition = $container->getDefinition($legacyId);
            if ($legacyBrickDefinition instanceof ChildDefinition && $legacyBrickDefinition->getParent() === AbstractAreabrick::class) {
                throw new InvalidDefinitionException(sprintf(
                    'Please use tag "%s" instead of "%s" to register your brick "%s" as a true toolbox area brick.',
                    'toolbox.area.brick',
                    'pimcore.area.brick',
                    $legacyId
                ));
            }

            foreach ($legacyTags as $pimcoreAreaBrickAttributes) {
                $additionalAreaBricksConfig[] = $pimcoreAreaBrickAttributes['id'];
            }

        }

        // register toolbox bricks
        $toolboxTaggedAreaBricksServices = $container->findTaggedServiceIds('toolbox.area.brick', true);
        foreach ($toolboxTaggedAreaBricksServices as $id => $tags) {
            $brickDefinition = $container->getDefinition($id);

            if (!$brickDefinition instanceof ChildDefinition) {
                throw new InvalidDefinitionException(sprintf(
                    'Areabrick "%s" needs to be a child of "%s"',
                    $id,
                    AbstractBaseAreabrick::class
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
                $type = AbstractBaseAreabrick::AREABRICK_TYPE_EXTERNAL;

                if (str_starts_with($id, 'ToolboxBundle')) {
                    $type = AbstractBaseAreabrick::AREABRICK_TYPE_INTERNAL;
                }

                if ($type === AbstractBaseAreabrick::AREABRICK_TYPE_EXTERNAL && in_array($attributes['id'], ToolboxConfig::TOOLBOX_TYPES, true)) {
                    throw new InvalidDefinitionException(sprintf(
                        'ID "%s" for AreaBrick "%s is a reserved identifier. Please change the id of your custom AreaBrick. Internal IDs are: %s.',
                        $attributes['id'],
                        $id,
                        implode(', ', ToolboxConfig::TOOLBOX_TYPES)
                    ));
                }

                $brickDefinition->addTag('pimcore.area.brick', ['id' => $attributes['id']]);
                $brickDefinition->addMethodCall('setAreaBrickType', [$type]);

                if ($brickDefinition->getParent() === AbstractBaseAreabrick::class) {
                    $notEditDialogAwareBricks[] = $attributes['id'];
                }
            }
        }

        // register simple toolbox bricks
        $toolboxTaggedSimpleAreaBricksServices = $container->findTaggedServiceIds('toolbox.area.simple_brick', true);
        foreach ($toolboxTaggedSimpleAreaBricksServices as $id => $tags) {
            $simpleBrickDefinition = $container->getDefinition($id);

            if (!$simpleBrickDefinition instanceof ChildDefinition) {
                throw new InvalidDefinitionException(sprintf(
                    'Simple Areabrick "%s" needs to be a child of "%s"',
                    $id,
                    AbstractBaseAreabrick::class
                ));
            }

            if ($simpleBrickDefinition->getClass() !== null) {
                throw new InvalidDefinitionException(sprintf('Simple Areabrick "%s" must not have a custom class!', $id));
            }

            $simpleBrickDefinition->setClass($simpleBrickDefinition->getParent() === AbstractAreabrick::class ? SimpleAreaBrickConfigurable::class : SimpleAreaBrick::class);
            $simpleBrickDefinition->addMethodCall('setAreaBrickType', [AbstractBaseAreabrick::AREABRICK_TYPE_EXTERNAL]);

            foreach ($tags as $attributes) {

                if (empty($attributes['title'])) {
                    throw new InvalidDefinitionException(sprintf('Simple Areabrick "%s" has an invalid title', $attributes['id']));
                }

                $simpleBrickDefinition->addMethodCall('setName', [$attributes['title']]);

                if (!empty($attributes['description'])) {
                    $simpleBrickDefinition->addMethodCall('setDescription', [$attributes['description']]);
                }

                if (!empty($attributes['template'])) {
                    $simpleBrickDefinition->addMethodCall('setTemplate', [$attributes['template']]);
                }

                if (!empty($attributes['icon'])) {
                    $simpleBrickDefinition->addMethodCall('setIcon', [$attributes['icon']]);
                }

                $simpleBrickDefinition->addTag('pimcore.area.brick', ['id' => $attributes['id']]);

                if ($simpleBrickDefinition->getParent() === AbstractBaseAreabrick::class) {
                    $notEditDialogAwareBricks[] = $attributes['id'];
                }

                $additionalAreaBricksConfig[] = $attributes['id'];
            }
        }

        if (count($notEditDialogAwareBricks) > 0) {
            $requestedEditDialogAwareBricks = $container->getParameter('toolbox.area_brick.dialog_aware_bricks');
            foreach ($requestedEditDialogAwareBricks as $requestedEditDialogAwareBrickId) {
                if (in_array($requestedEditDialogAwareBrickId, $notEditDialogAwareBricks, true)) {
                    throw new InvalidDefinitionException(
                        sprintf(
                            'Areabrick "%s" has some dialog editables but has been registered as a non-configurable brick. Please set "%s" as parent class or remove the config node from areas.%s',
                            $requestedEditDialogAwareBrickId,
                            AbstractAreabrick::class,
                            $requestedEditDialogAwareBrickId
                        )
                    );
                }
            }
        }

        if(count($additionalAreaBricksConfig) > 0) {
            $configManagerDefinition = $container->getDefinition(ConfigManager::class);
            $configManagerDefinition->addMethodCall('addAdditionalAreaConfig', [$additionalAreaBricksConfig]);
        }
    }
}
