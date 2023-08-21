<?php

namespace ToolboxBundle\Normalizer;

use Pimcore\Model\Document\Editable;

class LinkNormalizer implements PropertyNormalizerInterface
{
    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed
    {
        if (!$value instanceof Editable\Link) {
            return $value;
        }

        return [
            'href' => $value->getHref(),
            'data' => $value->getData()
        ];
    }
}
