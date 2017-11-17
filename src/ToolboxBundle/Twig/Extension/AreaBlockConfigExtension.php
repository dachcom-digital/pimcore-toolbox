<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\AreaManager;

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
        return [
            new \Twig_Function('toolbox_areablock_config', [$this, 'getAreaBlockConfiguration'], [
                'needs_context' => TRUE
            ])
        ];
    }

    /**
     * @param      $context
     * @param      $type
     *
     * @return array
     */
    public function getAreaBlockConfiguration($context = [], $type = NULL)
    {
        $document = $context['document'];
        return $this->areaManager->getAreaBlockConfiguration($type, $document instanceof Snippet);
    }

}