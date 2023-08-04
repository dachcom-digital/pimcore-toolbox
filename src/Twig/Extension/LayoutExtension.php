<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\LayoutManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    public function __construct(protected LayoutManager $layoutManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_area_path', [$this, 'getAreaPath'])
        ];
    }

    public function getAreaPath($areaId, $areaTemplateDir, $viewName = 'view'): string
    {
        return $this->layoutManager->getAreaTemplatePath($areaId, $areaTemplateDir, $viewName);
    }
}
