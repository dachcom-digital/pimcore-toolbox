<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractAreabrick as PimcoreAbstractAreabrick;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxInterface;
use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManager;
use ToolboxBundle\ToolboxConfig;

abstract class AbstractAreabrick extends PimcoreAbstractAreabrick implements EditableDialogBoxInterface
{
    public const AREABRICK_TYPE_INTERNAL = 'internal';
    public const AREABRICK_TYPE_EXTERNAL = 'external';

    protected ConfigManagerInterface $configManager;
    protected BrickConfigBuilder $brickConfigBuilder;
    protected LayoutManager $layoutManager;
    public string $areaBrickType = 'internal';

    public function setAreaBrickType(string $type = self::AREABRICK_TYPE_INTERNAL): void
    {
        $this->areaBrickType = $type;
    }

    public function getAreaBrickType(): string
    {
        return $this->areaBrickType;
    }

    public function setConfigManager(ConfigManagerInterface $configManager): void
    {
        $this->configManager = $configManager;
    }

    public function getConfigManager(): ConfigManagerInterface
    {
        $space = $this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL
            ? ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL
            : ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL;

        return $this->configManager->setAreaNameSpace($space);
    }

    public function setBrickConfigBuilder(BrickConfigBuilder $brickConfigBuilder): void
    {
        $this->brickConfigBuilder = $brickConfigBuilder;
    }

    public function getBrickConfigBuilder(): BrickConfigBuilder
    {
        return $this->brickConfigBuilder;
    }

    public function setLayoutManager(LayoutManager $layoutManager): void
    {
        $this->layoutManager = $layoutManager;
    }

    public function getEditableDialogBoxConfiguration(Document\Editable $area, ?Document\Editable\Area\Info $info): EditableDialogBoxConfiguration
    {
        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());
        $themeOptions = $this->getConfigManager()->getConfig('theme');

        return $this->brickConfigBuilder->buildDialogBoxConfiguration($info, $this->getId(), $configNode, $themeOptions);
    }

    /**
     * @throws \Exception
     */
    public function action(Document\Editable\Area\Info $info): ?Response
    {
        if (!$this->getConfigManager() instanceof ConfigManagerInterface) {
            throw new \Exception('Please register your AreaBrick "' . $info->getId() . '" as a service and set "toolbox.area.brick.base_brick" as parent.');
        } elseif ($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL && !in_array($info->getId(), ToolboxConfig::TOOLBOX_TYPES)) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick has a invalid AreaBrickType. Please set type to "' . self::AREABRICK_TYPE_EXTERNAL . '".');
        } elseif ($this->getAreaBrickType() === self::AREABRICK_TYPE_EXTERNAL && in_array($info->getId(), ToolboxConfig::TOOLBOX_TYPES)) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick is using a reserved id. Please change the id of your custom AreaBrick.');
        }

        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());

        $info->setParams([
            'additionalClassesData' => $this->configureAdditionalClasses($info, $configNode),
            'elementThemeConfig'    => $this->layoutManager->getAreaThemeConfig($this->getId()),
            'areaId'                => $this->getId()
        ]);

        return null;
    }

    private function configureAdditionalClasses(Document\Editable\Area\Info $info, array $configNode): array
    {
        $classesArray = [];

        if (!isset($configNode['config_elements'])) {
            return $classesArray;
        }

        foreach ($configNode['config_elements'] as $name => $configElement) {
            if (!isset($configElement['type'])) {
                continue;
            }

            if ($configElement['type'] === 'additionalClasses') {
                $addClassField = $this->getDocumentEditable($info->getDocument(), 'select', 'add_classes');
                if ($addClassField instanceof Document\Editable\Select && !empty($addClassField->getValue())) {
                    $classesArray[] = (string) $addClassField->getValue();
                }
            } elseif ($configElement['type'] === 'additionalClassesChained') {
                $chainedElementName = explode('_', $name);
                $chainedIncrementor = end($chainedElementName);
                $addChainedClassField = $this->getDocumentEditable($info->getDocument(), 'select', 'add_cclasses_' . $chainedIncrementor);
                if ($addChainedClassField instanceof Document\Editable\Select && !empty($addChainedClassField->getValue())) {
                    $classesArray[] = (string) $addChainedClassField->getValue();
                }
            }
        }

        return $classesArray;
    }

    public function getTemplatePath(string $viewName = 'view'): string
    {
        return $this->layoutManager->getAreaTemplatePath($this->getId(), $viewName);
    }

    public function getTemplateLocation(): string
    {
        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL) {
            return static::TEMPLATE_LOCATION_BUNDLE;
        }

        return static::TEMPLATE_LOCATION_GLOBAL;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        // return null by default = auto-discover
        return null;
    }

    public function getTemplateSuffix()
    {
        return static::TEMPLATE_SUFFIX_TWIG;
    }

    public function getIcon(): ?string
    {
        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_EXTERNAL) {
            return null;
        }

        return '/bundles/toolbox/areas/' . $this->getId() . '/icon.svg';
    }

    public function getHtmlTagOpen(Document\Editable\Area\Info $info): string
    {
        return '';
    }

    public function getHtmlTagClose(Document\Editable\Area\Info $info): string
    {
        return '';
    }
}
