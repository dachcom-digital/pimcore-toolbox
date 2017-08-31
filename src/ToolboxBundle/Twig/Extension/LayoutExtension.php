<?php

namespace ToolboxBundle\Twig\Extension;

use Symfony\Component\Templating\EngineInterface;
use ToolboxBundle\Service\LayoutManager;

class LayoutExtension extends \Twig_Extension
{
    /**
     * @var LayoutManager
     */
    protected $layoutManager;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * LayoutExtension constructor.
     *
     * @param LayoutManager $layoutManager
     * @param EngineInterface $templating
     */
    public function __construct(LayoutManager $layoutManager, EngineInterface $templating)
    {
        $this->layoutManager = $layoutManager;
        $this->templating = $templating;
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
        return $this->layoutManager->setTemplating($this->templating)->getAreaTemplatePath($areaId, $viewName);
    }
}