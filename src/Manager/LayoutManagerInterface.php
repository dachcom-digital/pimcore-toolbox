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
