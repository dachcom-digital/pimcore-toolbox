<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AreaBlockConfigExtension extends AbstractExtension
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @var AreaManagerInterface
     */
    protected $areaManager;

    /**
     * @param ConfigManagerInterface $configManager
     * @param AreaManagerInterface   $areaManager
     */
    public function __construct(ConfigManagerInterface $configManager, AreaManagerInterface $areaManager)
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
            new TwigFunction('toolbox_areablock_config', [$this, 'getAreaBlockConfiguration'], [
                'needs_context' => true
            ])
        ];
    }

    /**
     * @param array $context
     * @param null  $type
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getAreaBlockConfiguration($context = [], $type = null)
    {
        $document = $context['document'];

        return $this->areaManager->getAreaBlockConfiguration($type, $document instanceof Snippet);
    }
}
