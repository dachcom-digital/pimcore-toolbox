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

namespace ToolboxBundle\Normalizer;

use Pimcore\Model\Asset;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Service\AssetService;

class GalleryRelationsNormalizer implements PropertyNormalizerInterface
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected AssetService $assetService
    ) {
    }

    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $normalizedData = [];

        foreach ($value as $asset) {
            if (!$asset instanceof Asset\Image) {
                continue;
            }

            $galleryLightbox = $this->configManager->getImageThumbnailFromConfig('gallery_lightbox');
            $galleryElement = $this->configManager->getImageThumbnailFromConfig('gallery_element');
            $galleryThumbnail = $this->configManager->getImageThumbnailFromConfig('gallery_thumbnail');

            $normalizedData[] = [
                'lightbox'  => $this->assetService->generateImageThumbnail($asset, $galleryLightbox),
                'image'     => $this->assetService->generateImageThumbnail($asset, $galleryElement),
                'thumbnail' => $this->assetService->generateImageThumbnail($asset, $galleryThumbnail),
            ];
        }

        return $normalizedData;
    }
}
