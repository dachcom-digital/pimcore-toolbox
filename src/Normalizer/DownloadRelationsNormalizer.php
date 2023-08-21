<?php

namespace ToolboxBundle\Normalizer;

use ToolboxBundle\Service\DownloadInfoService;

class DownloadRelationsNormalizer implements PropertyNormalizerInterface
{
    public function __construct(protected DownloadInfoService $downloadInfoService)
    {
    }

    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $normalizedData = [];

        foreach ($value as $asset) {

            $normalizedDownloadInfo = $this->downloadInfoService->getDownloadInfo($asset, true, 'optimized');

            if (array_key_exists('previewImage', $normalizedDownloadInfo)) {
                unset($normalizedDownloadInfo['previewImage']);
            }

            $normalizedData[] = $normalizedDownloadInfo;
        }

        return $normalizedData;
    }
}
