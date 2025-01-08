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

use Pimcore\Model\Document\Editable;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Service\AssetService;

class ImageEditableNormalizer implements PropertyNormalizerInterface
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected AssetService $assetService
    ) {
    }

    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed
    {
        if (!$value instanceof Editable\Image) {
            return $value;
        }

        $config = $value->getConfig();

        $imageLightbox = array_key_exists('lightbox_thumbnail', $config)
            ? $config['lightbox_thumbnail']
            : $this->configManager->getImageThumbnailFromConfig('image_lightbox');

        $imageElement = array_key_exists('thumbnail', $config)
            ? $config['thumbnail']
            : $this->configManager->getImageThumbnailFromConfig('image_element');

        if ($value->getThumbnailConfig()) {
            $imageElement = $value->getThumbnailConfig();
        }

        return [
            'caption'  => $value->getText(),
            'hotspots' => $value->getHotspots(),
            'marker'   => $value->getMarker(),
            'lightbox' => $this->assetService->generateImageThumbnailFromEditable($value, $imageLightbox),
            'image'    => $this->assetService->generateImageThumbnailFromEditable($value, $imageElement),
        ];
    }
}
