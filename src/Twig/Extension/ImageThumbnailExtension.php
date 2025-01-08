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

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageThumbnailExtension extends AbstractExtension
{
    public function __construct(protected ConfigManagerInterface $configManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_get_image_thumbnail', [$this, 'getImageThumbnail'])
        ];
    }

    /**
     * @throws \Exception
     */
    public function getImageThumbnail(string $thumbnailName): ?string
    {
        return $this->configManager->getImageThumbnailFromConfig($thumbnailName);
    }
}
