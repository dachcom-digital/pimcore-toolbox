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

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\LayoutManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    public function __construct(protected LayoutManager $layoutManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_area_path', [$this, 'getAreaPath'])
        ];
    }

    public function getAreaPath($areaId, $areaTemplateDir, $viewName = 'view'): string
    {
        return $this->layoutManager->getAreaTemplatePath($areaId, $areaTemplateDir, $viewName);
    }
}
