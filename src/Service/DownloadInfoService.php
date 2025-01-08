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

namespace ToolboxBundle\Service;

use Pimcore\Model\Asset;
use Symfony\Contracts\Translation\TranslatorInterface;
use ToolboxBundle\Connector\BundleConnector;
use ToolboxBundle\Manager\ConfigManagerInterface;

class DownloadInfoService
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected BundleConnector $bundleConnector,
        protected TranslatorInterface $translator
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getDownloadInfo(
        Asset $download,
        bool $showPreviewImage = false,
        string $fileSizeUnit = 'optimized',
        int $fileSizePrecision = 0,
        bool $showFileNameIfTitleEmpty = false
    ): array {
        if (
            $this->bundleConnector->hasBundle('MembersBundle') === true &&
            $this->bundleConnector->getBundleService(\MembersBundle\Manager\RestrictionManager::class)->elementIsInProtectedStorageFolder($download)
        ) {
            $dPath = $this->bundleConnector->getBundleService(\MembersBundle\Security\RestrictionUri::class)->generateAssetUrl($download);
        } else {
            $dPath = $download->getFullPath();
        }

        if ($fileSizeUnit === 'optimized') {
            $realSize = $download->getFileSize();
            $dSize = $this->getOptimizedFileSize($realSize, $fileSizePrecision);
        } else {
            $dSize = $download->getFileSize($fileSizeUnit, $fileSizePrecision);
        }

        $dType = pathinfo($download->getFilename(), PATHINFO_EXTENSION);
        $downloadTitle = $showFileNameIfTitleEmpty ? $download->getFilename() : $this->translator->trans('Download');
        $dName = ($download->getMetadata('title')) ?: $downloadTitle;
        $dAltText = $download->getMetadata('alt') ?: '';
        $dImageAltText = !empty($dAltText) ? $dAltText : $dName;

        $dPreviewImage = null;
        $previewThumbName = $this->configManager->getImageThumbnailFromConfig('download_preview_thumbnail');

        if ($showPreviewImage) {
            $metaPreviewImage = $download->getMetadata('previewImage');
            /* @phpstan-ignore-next-line */
            if ($metaPreviewImage instanceof Asset\Image) {
                $dPreviewImage = $metaPreviewImage->getThumbnail($previewThumbName);
            } elseif ($download instanceof Asset\Image) {
                $dPreviewImage = $download->getThumbnail($previewThumbName);
            } elseif ($download instanceof Asset\Document) {
                $dPreviewImage = $download->getImageThumbnail($previewThumbName);
            }
        }

        $dPreviewImagePath = null;
        $hasPreviewImage = false;

        if ($dPreviewImage instanceof Asset\Image\Thumbnail) {
            $dPreviewImagePath = $dPreviewImage->getPath();
            $hasPreviewImage = true;
        } elseif ($dPreviewImage instanceof Asset\Document\ImageThumbnail && !empty($dPreviewImage->getConfig())) {
            $dPreviewImagePath = $dPreviewImage->getPath();
            $hasPreviewImage = true;
        }

        return [
            'path'             => $dPath,
            'size'             => $dSize,
            'type'             => $dType,
            'name'             => $dName,
            'altText'          => $dAltText,
            'imageAltText'     => $dImageAltText,
            'previewImage'     => $dPreviewImage,
            'hasPreviewImage'  => $hasPreviewImage,
            'previewImagePath' => $dPreviewImagePath
        ];
    }

    public function getOptimizedFileSize(mixed $bytes, int $precision): string
    {
        if ($bytes === 1) {
            return '1 Byte';
        }
        // https://gist.github.com/liunian/9338301?permalink_comment_id=1804497#gistcomment-1804497
        static $units = ['Bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;
        while (($bytes / $step) > 0.9) {
            $bytes = $bytes / $step;
            $i++;
        }

        return round($bytes, $precision) . ' ' . ($units[$i] ?? '');
    }
}
