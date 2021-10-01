<?php

namespace ToolboxBundle\Manager;

use Symfony\Component\Templating\EngineInterface;

interface LayoutManagerInterface
{
    public function setTemplating(EngineInterface $templating): void;

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
