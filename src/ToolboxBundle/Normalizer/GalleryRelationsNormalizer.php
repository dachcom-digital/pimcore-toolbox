<?php

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
