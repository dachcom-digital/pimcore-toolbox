<?php

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

        $imageLightbox = $this->configManager->getImageThumbnailFromConfig('image_lightbox');
        $imageElement = $this->configManager->getImageThumbnailFromConfig('image_element');

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
