<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

use ToolboxBundle\Service\BrickConfigBuilder;
use ToolboxBundle\Service\ConfigManager;
use ToolboxBundle\Service\LayoutManager;

abstract class AbstractAreabrick extends AbstractTemplateAreabrick
{
    /**
     * @var ConfigManager
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
    var $areaBrickType = 'internal';

    const AREABRICK_TYPE_INTERNAL = 'internal';

    const AREABRICK_TYPE_EXTERNAL = 'external';

    /**
     * @param string $type
     */
    public function setAreaBrickType($type = self::AREABRICK_TYPE_INTERNAL) {

        $this->areaBrickType = $type;
    }

    /**
     * @return string
     */
    public function getAreaBrickType() {

        return $this->areaBrickType;
    }

    /**
     * @param ConfigManager $configManager
     */
    public function setConfigManager(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return \ToolboxBundle\Service\ConfigManager object
     */
    public function getConfigManager()
    {
        $space = $this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL
            ? ConfigManager::AREABRICK_NAMESPACE_INTERNAL
            : ConfigManager::AREABRICK_NAMESPACE_EXTERNAL;

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
     * @return \ToolboxBundle\Service\BrickConfigBuilder
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
     * @param Info $info
     *
     * @throws \Exception
     */
    public function action(Info $info)
    {
        if(!$this->getConfigManager() instanceof ConfigManager) {
            throw new \Exception('Please register your AreaBrick "' . $info->getId() . '" as a service and set "toolbox.area.brick.base_brick" as parent.');
        } else if($this->getAreaBrickType() == self::AREABRICK_TYPE_INTERNAL && !in_array($info->getId(), $this->configManager->getValidCoreBricks())) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick has a invalid AreaBrickType. Please set type to "' . self::AREABRICK_TYPE_EXTERNAL . '".');
        } else if($this->getAreaBrickType() == self::AREABRICK_TYPE_EXTERNAL && in_array($info->getId(), $this->configManager->getValidCoreBricks())) {
            throw new \Exception('The "' . $info->getId() . '" AreaBrick is using a reserved id. Please change the id of your custom AreaBrick.');
        }

        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());
        $themeOptions = $this->getConfigManager()->getConfig('theme');
        $configWindowData = $this->getBrickConfigBuilder()->buildElementConfig($this->getId(), $this->getName(), $info, $configNode, $themeOptions);

        $layoutDir = NULL;

        $view = $info->getView();
        $view->elementConfigBar = $configWindowData;
        $view->elementThemeConfig = $this->layoutManager->getAreaThemeConfig($this->getId());
        $view->areaId = $this->getId();

    }

    /**
     * Internal Areas: load from Bundle
     * External Areas: defined in AppBundle with a view in /app/Resources/views/Areas/*
     *
     * @inheritDoc
     */
    public function getTemplateLocation()
    {
        if($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL) {
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
     * @inheritDoc
     */
    public function getTemplateSuffix()
    {
        return static::TEMPLATE_SUFFIX_TWIG;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTagOpen(Info $info)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTagClose(Info $info)
    {
        return '';
    }

}