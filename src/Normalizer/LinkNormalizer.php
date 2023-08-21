<?php

namespace ToolboxBundle\Normalizer;

use Pimcore\Model\Document\Editable;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Service\AssetService;

class LinkNormalizer implements PropertyNormalizerInterface
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected AssetService $assetService
    ) {
    }

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
