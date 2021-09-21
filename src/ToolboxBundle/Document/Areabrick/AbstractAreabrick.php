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
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @var BrickConfigBuilder
     */
    protected $brickConfigBuilder;

    /**
     * @var LayoutManager
     */
    protected $layoutManager;

    /**
     * @var string
     */
    public $areaBrickType = 'internal';

    const AREABRICK_TYPE_INTERNAL = 'internal';

    const AREABRICK_TYPE_EXTERNAL = 'external';

    /**
     * @param string $type
     */
    public function setAreaBrickType($type = self::AREABRICK_TYPE_INTERNAL)
    {
        $this->areaBrickType = $type;
    }

    /**
     * @return string
     */
    public function getAreaBrickType()
    {
        return $this->areaBrickType;
    }

    /**
     * @param ConfigManagerInterface $configManager
     */
    public function setConfigManager(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return \ToolboxBundle\Manager\ConfigManagerInterface object
     */
    public function getConfigManager()
    {
        $space = $this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL
            ? ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL
            : ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL;

        return $this->configManager->setAreaNameSpace($space);
    }

    /**
     * @param BrickConfigBuilder $brickConfigBuilder
     */
    public function setBrickConfigBuilder(BrickConfigBuilder $brickConfigBuilder)
    {
        $this->brickConfigBuilder = $brickConfigBuilder;
    }

    /**
     * @return BrickConfigBuilder
     */
    public function getBrickConfigBuilder()
    {
        return $this->brickConfigBuilder;
    }

    /**
     * @param LayoutManager $layoutManager
     */
    public function setLayoutManager(LayoutManager $layoutManager)
    {
        $this->layoutManager = $layoutManager;
    }

    /**
     * {@inheritdoc}
     */
    public function action(Editable\Area\Info $info)
    {
        if (!$this->getConfigManager() instanceof ConfigManagerInterface) {
            throw new \Exception('Please register your AreaBrick "' . $info->getId() . '" as a service and set "toolbox.area.brick.base_brick" as parent.');
        } elseif ($this->getAreaBrickType() == self::AREABRICK_TYPE_INTERNAL && !in_array($info->getId(), ToolboxConfig::TOOLBOX_TYPES)) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick has a invalid AreaBrickType. Please set type to "' . self::AREABRICK_TYPE_EXTERNAL . '".');
        } elseif ($this->getAreaBrickType() == self::AREABRICK_TYPE_EXTERNAL && in_array($info->getId(), ToolboxConfig::TOOLBOX_TYPES)) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick is using a reserved id. Please change the id of your custom AreaBrick.');
        }

        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());
        $themeOptions = $this->getConfigManager()->getConfig('theme');
        $configWindowData = $this->getBrickConfigBuilder()->buildElementConfig($this->getId(), $this->getName(), $info, $configNode, $themeOptions);

        $layoutDir = null;

        $view = $info->getView();
        $view->getParameters()->add([
            'elementConfigBar'      => $configWindowData,
            'additionalClassesData' => $this->configureAdditionalClasses($info, $configNode),
            'elementThemeConfig'    => $this->layoutManager->getAreaThemeConfig($this->getId()),
            'areaId'                => $this->getId()
        ]);

        return null;
    }

    /**
     * @param Editable\Area\Info $info
     * @param array         $configNode
     *
     * @return array
     */
    private function configureAdditionalClasses(Editable\Area\Info $info, $configNode)
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
                $addClassField = $this->getDocumentTag($info->getDocument(), 'select', 'add_classes');
                if ($addClassField instanceof Editable\Select && !empty($addClassField->getValue())) {
                    $classesArray[] = (string) $addClassField->getValue();
                }
            } elseif ($configElement['type'] === 'additionalClassesChained') {
                $chainedElementName = explode('_', $name);
                $chainedIncrementor = end($chainedElementName);
                $addChainedClassField = $this->getDocumentTag($info->getDocument(), 'select', 'add_cclasses_' . $chainedIncrementor);
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
     *
     * {@inheritdoc}
     */
    public function getTemplateLocation()
    {
        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL) {
            return static::TEMPLATE_LOCATION_BUNDLE;
        }

        return static::TEMPLATE_LOCATION_GLOBAL;
    }

    /**
     * @param string $viewName
     *
     * @return string
     */
    public function getTemplatePath($viewName = 'view')
    {
        return $this->layoutManager->getAreaTemplatePath($this->getId(), $viewName);
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        if ($this->getAreaBrickType() == self::AREABRICK_TYPE_EXTERNAL) {
            return null;
        }

        return '/bundles/toolbox/areas/' . $this->getId() . '/icon.svg';
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTagOpen(Editable\Area\Info $info)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTagClose(Editable\Area\Info $info)
    {
        return '';
    }
}
