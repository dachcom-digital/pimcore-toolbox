<?php

namespace ToolboxBundle\Manager;

use Symfony\Component\Templating\EngineInterface;

interface LayoutManagerInterface
{
    public function setTemplating(EngineInterface $templating): static;

    public function getAreaTemplateDir(?string $areaId = null, string $viewName = 'view', string $extension = 'html.twig');

    public function getAreaTemplatePath(?string $areaId = null, string $viewName = 'view', string $extension = 'html.twig'): string;

    public function getAreaThemeConfig(string $areaName = ''): array;
}
