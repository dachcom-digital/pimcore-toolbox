<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\LayoutManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    /**
     * @var LayoutManager
     */
    protected $layoutManager;

    /**
     * @param LayoutManager $layoutManager
     */
    public function __construct(LayoutManager $layoutManager)
    {
        $this->layoutManager = $layoutManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('toolbox_area_path', [$this, 'getAreaPath'])
        ];
    }

    public function getAreaPath($areaId, $viewName = 'view')
    {
        return $this->layoutManager->getAreaTemplatePath($areaId, $viewName);
    }
}
