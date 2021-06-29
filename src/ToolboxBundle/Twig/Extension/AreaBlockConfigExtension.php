<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AreaBlockConfigExtension extends AbstractExtension
{
    protected ConfigManagerInterface $configManager;
    protected AreaManagerInterface $areaManager;

    public function __construct(ConfigManagerInterface $configManager, AreaManagerInterface $areaManager)
    {
        $this->configManager = $configManager;
        $this->areaManager = $areaManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_areablock_config', [$this, 'getAreaBlockConfiguration'], [
                'needs_context' => true
            ])
        ];
    }

    public function getAreaBlockConfiguration(array $context = [], string $type = null): array
    {
        $document = $context['document'];

        return $this->areaManager->getAreaBlockConfiguration($type, $document instanceof Snippet);
    }
}
