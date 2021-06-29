<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Editable;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManager;
use ToolboxBundle\ToolboxConfig;

abstract class AbstractAreabrick extends AbstractTemplateAreabrick
{
    public const AREABRICK_TYPE_INTERNAL = 'internal';

    public const AREABRICK_TYPE_EXTERNAL = 'external';

    protected ConfigManagerInterface $configManager;
    protected BrickConfigBuilder $brickConfigBuilder;
    protected LayoutManager $layoutManager;

    public $areaBrickType = 'internal';

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

    public function action(Editable\Area\Info $info)
    {
        if (!$this->getConfigManager() instanceof ConfigManagerInterface) {
            throw new \Exception('Please register your AreaBrick "' . $info->getId() . '" as a service and set "toolbox.area.brick.base_brick" as parent.');
        }

        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL && !in_array($info->getId(), ToolboxConfig::TOOLBOX_TYPES)) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick has a invalid AreaBrickType. Please set type to "' . self::AREABRICK_TYPE_EXTERNAL . '".');
        }

        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_EXTERNAL && in_array($info->getId(), ToolboxConfig::TOOLBOX_TYPES)) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick is using a reserved id. Please change the id of your custom AreaBrick.');
        }

        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());
        $themeOptions = $this->getConfigManager()->getConfig('theme');
        $configWindowData = $this->getBrickConfigBuilder()->buildElementConfig($this->getId(), $this->getName(), $info, $configNode, $themeOptions);

        $info->setParams([
            'elementConfigBar'      => $configWindowData,
            'additionalClassesData' => $this->configureAdditionalClasses($info, $configNode),
            'elementThemeConfig'    => $this->layoutManager->getAreaThemeConfig($this->getId()),
            'areaId'                => $this->getId()
        ]);

        return null;
    }

    private function configureAdditionalClasses(Editable\Area\Info $info, array $configNode): array
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
                if ($addClassField instanceof Editable\Select && !empty($addClassField->getValue())) {
                    $classesArray[] = (string) $addClassField->getValue();
                }
            } elseif ($configElement['type'] === 'additionalClassesChained') {
                $chainedElementName = explode('_', $name);
                $chainedIncrementor = end($chainedElementName);
                $addChainedClassField = $this->getDocumentEditable($info->getDocument(), 'select', 'add_cclasses_' . $chainedIncrementor);
                if ($addChainedClassField instanceof Editable\Select && !empty($addChainedClassField->getValue())) {
                    $classesArray[] = (string) $addChainedClassField->getValue();
                }
            }
        }

        return $classesArray;
    }

    /**
     * Internal Areas: load from Bundle
     * External Areas: defined in AppBundle with a view in /app/Resources/views/Areas/*.
     */
    public function getTemplateLocation(): string
    {
        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL) {
            return static::TEMPLATE_LOCATION_BUNDLE;
        }

        return static::TEMPLATE_LOCATION_GLOBAL;
    }

    public function getTemplatePath(string $viewName = 'view'): string
    {
        return $this->layoutManager->getAreaTemplatePath($this->getId(), $viewName);
    }

    public function getTemplateSuffix(): string
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

    public function getHtmlTagOpen(Editable\Area\Info $info): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTagClose(Editable\Area\Info $info): string
    {
        return '';
    }
}
