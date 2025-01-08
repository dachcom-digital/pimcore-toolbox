<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Manager;

use Symfony\Component\Templating\EngineInterface;

class LayoutManager implements LayoutManagerInterface
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected EngineInterface $templating
    ) {
    }

    public function getAreaTemplateDir(string $areaId, string $areaTemplateDir, string $viewName = 'view', string $extension = 'html.twig'): string
    {
        $elementThemeConfig = $this->getAreaThemeConfig($areaId);

        $pathStructure = '@Toolbox/toolbox/%s/%s';

        $defaultDir = sprintf(
            $pathStructure,
            strtolower($elementThemeConfig['layout']),
            $areaTemplateDir
        );

        // no fallback layout defined, return default
        if (empty($elementThemeConfig['default_layout'])) {
            return $defaultDir;
        }

        if ($this->templating->exists($defaultDir . DIRECTORY_SEPARATOR . $viewName . '.' . $extension)) {
            return $defaultDir;
        }

        // return fallback layout
        return sprintf(
            $pathStructure,
            $elementThemeConfig['default_layout'],
            $areaTemplateDir
        );
    }

    public function getAreaTemplatePath($areaId, string $areaTemplateDir, string $viewName = 'view', string $extension = 'html.twig'): string
    {
        return $this->getAreaTemplateDir($areaId, $areaTemplateDir, $viewName) . DIRECTORY_SEPARATOR . $viewName . '.' . $extension;
    }

    public function getAreaThemeConfig(string $areaName): array
    {
        $layoutConfiguration = $this->configManager->getConfig('theme');

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
