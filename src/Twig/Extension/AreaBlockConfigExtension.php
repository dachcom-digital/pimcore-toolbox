<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AreaBlockConfigExtension extends AbstractExtension
{
    public function __construct(protected ConfigManagerInterface $configManager, protected AreaManagerInterface $areaManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_areablock_config', [$this, 'getAreaBlockConfiguration'], [
                'needs_context' => true
            ])
        ];
    }

    /**
     * @throws \Exception
     */
    public function getAreaBlockConfiguration(array $context = [], $type = null): array
    {
        $document = $context['document'];
        $editMode = $context['editmode'] ?? false;

        return $this->areaManager->getAreaBlockConfiguration($type, $document instanceof Snippet, $editMode);
    }
}
