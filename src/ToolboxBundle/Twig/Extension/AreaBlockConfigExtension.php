<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Service\ConfigManager;
use ToolboxBundle\Service\AreaManager;

class AreaBlockConfigExtension extends \Twig_Extension
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var AreaManager
     */
    protected $areaManager;

    /**
     * AreaBlockConfigExtension constructor.
     *
     * @param ConfigManager $configManager
     * @param AreaManager   $areaManager
     */
    public function __construct(ConfigManager $configManager, AreaManager $areaManager)
    {
        $this->configManager = $configManager;
        $this->areaManager = $areaManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('toolbox_areablock_config', [$this, 'getAreaBlockConfiguration'])];
    }

    /**
     * @param      $type
     * @param bool $fromSnippet
     *
     * @return array
     */
    public function getAreaBlockConfiguration($type = NULL, $fromSnippet = FALSE)
    {
        return $this->areaManager->getAreaBlockConfiguration($type, $fromSnippet);
    }

}