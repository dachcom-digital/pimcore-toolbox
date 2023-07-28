<?php

namespace ToolboxBundle\Manager;

interface LayoutManagerInterface
{
    public const TOOLBOX_LAYOUT_BOOTSTRAP3 = 'Bootstrap3';
    public const TOOLBOX_LAYOUT_BOOTSTRAP4 = 'Bootstrap4';
    public const TOOLBOX_LAYOUT_UIKIT3 = 'UIkit3';
    public const TOOLBOX_LAYOUT_HEADLESS = 'Headless';

    /**
     * @throws \Exception
     */
    public function getAreaTemplateDir(string $areaId, string $areaTemplateDir, string $viewName = 'view', string $extension = 'html.twig'): string;

    /**
     * @throws \Exception
     */
    public function getAreaTemplatePath(string $areaId, string $areaTemplateDir, string $viewName = 'view', string $extension = 'html.twig'): string;

    /**
     * @throws \Exception
     */
    public function getAreaThemeConfig(string $areaName): array;
}
