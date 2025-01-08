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
