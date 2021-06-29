<?php

namespace ToolboxBundle\Manager;

use Symfony\Component\Templating\EngineInterface;

class LayoutManager implements LayoutManagerInterface
{
    protected ConfigManager $configManager;
    protected EngineInterface $templating;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function setTemplating(EngineInterface $templating): static
    {
        $this->templating = $templating;

        return $this;
    }

    public function getAreaTemplateDir(?string $areaId = null, string $viewName = 'view', string $extension = 'html.twig'): string
    {
        $elementThemeConfig = $this->getAreaThemeConfig($areaId);

        $pathStructure = '@Toolbox/Toolbox/%s/%s';

        $defaultDir = sprintf(
            $pathStructure,
            $elementThemeConfig['layout'],
            ucfirst($areaId)
        );

        //no fallback layout defined. return default.
        if ($elementThemeConfig['default_layout'] === false || empty($elementThemeConfig['default_layout'])) {
            return $defaultDir;
        }

        if ($this->templating->exists($defaultDir . DIRECTORY_SEPARATOR . $viewName . '.' . $extension)) {
            return $defaultDir;
        }

        //return fallback layout.
        return sprintf(
            $pathStructure,
            $elementThemeConfig['default_layout'],
            ucfirst($areaId)
        );
    }

    public function getAreaTemplatePath(?string $areaId = null, string $viewName = 'view', string $extension = 'html.twig'): string
    {
        return $this->getAreaTemplateDir($areaId, $viewName) . DIRECTORY_SEPARATOR . $viewName . '.' . $extension;
    }

    public function getAreaThemeConfig(string $areaName = ''): array
    {
        $layoutConfiguration = $this->configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getConfig('theme');

        $theme = [
            'layout'         => $layoutConfiguration['layout'],
            'default_layout' => $layoutConfiguration['default_layout'],
            'wrapper'        => false
        ];

        if (isset($layoutConfiguration['wrapper'][$areaName]['wrapper_classes'])) {
            $theme['wrapper'] = $layoutConfiguration['wrapper'][$areaName]['wrapper_classes'];
        }

        return $theme;
    }
}
