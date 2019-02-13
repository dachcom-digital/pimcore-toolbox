<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\LayoutManager;

class LayoutExtension extends \Twig_Extension
{
    /**
     * @var LayoutManager
     */
    protected $layoutManager;

    /**
     * LayoutExtension constructor.
     *
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
            new \Twig_Function('toolbox_area_path', [$this, 'getAreaPath'])
        ];
    }

    public function getAreaPath($areaId, $viewName = 'view')
    {
        return $this->layoutManager->getAreaTemplatePath($areaId, $viewName);
    }
}
