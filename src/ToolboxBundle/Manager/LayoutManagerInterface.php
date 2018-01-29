<?php

namespace ToolboxBundle\Manager;

use Symfony\Component\Templating\EngineInterface;

interface LayoutManagerInterface
{
    public function setTemplating(EngineInterface $templating);

    /**
     * @param null $areaId
     * @param string $viewName
     * @param string $extension
     *
     * @return string
     */
    public function getAreaTemplateDir($areaId = NULL, $viewName = 'view', $extension = 'html.twig');

    /**
     * @param null   $areaId
     * @param string $viewName
     * @param string $extension
     *
     * @return string
     */
    public function getAreaTemplatePath($areaId = NULL, $viewName = 'view', $extension = 'html.twig');

    /**
     * @param string $areaName
     * @return array
     * @throws \Exception
     */
    public function getAreaThemeConfig($areaName = '');
}