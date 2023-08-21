<?php

namespace ToolboxBundle\Normalizer;

use Pimcore\Model\Asset\Image\Thumbnail;

class ThumbnailNormalizer implements PropertyNormalizerInterface
{
    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed
    {
        if (!$value instanceof Thumbnail) {
            return $value;
        }

        return $value->getPath();
    }
}
