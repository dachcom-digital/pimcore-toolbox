<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\LayoutManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    protected LayoutManager $layoutManager;

    public function __construct(LayoutManager $layoutManager)
    {
        $this->layoutManager = $layoutManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_area_path', [$this, 'getAreaPath'])
        ];
    }

    public function getAreaPath(string $areaId, string $viewName = 'view'): string
    {
        return $this->layoutManager->getAreaTemplatePath($areaId, $viewName);
    }
}
