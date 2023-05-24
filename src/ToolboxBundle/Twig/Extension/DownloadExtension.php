<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\File;
use Pimcore\Model\Asset;
use ToolboxBundle\Connector\BundleConnector;
use Pimcore\Translation\Translator;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DownloadExtension extends AbstractExtension
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected BundleConnector $bundleConnector,
        protected Translator $translator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_download_info', [$this, 'getDownloadInfo']),
            new TwigFunction(
                'toolbox_download_tracker',
                [$this, 'getDownloadTracker'],
                ['is_safe' => ['html']]
            )
        ];
    }

    /**
     * @throws \Exception
     */
    public function getDownloadTracker(mixed $areaType, mixed $element = null): string
    {
        if (empty($areaType)) {
            return '';
        }

        if (is_array($areaType)) {
            $trackerInfo = $areaType;
        } else {
            $configNode = $this->configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig($areaType);

            if (empty($configNode) || !isset($configNode['event_tracker'])) {
                return '';
            }

            $trackerInfo = $configNode['event_tracker'];
        }

        $str = 'data-tracking="active" ';

        $str .= implode(' ', array_map(static function ($key) use ($trackerInfo, $element) {
            $val = $trackerInfo[$key];

            if (is_bool($val)) {
                $val = (int) $val;
            }

            if ($key === 'label' && is_array($val)) {
                $getter = $val;
                $val = call_user_func_array([$element, $getter[0]], $getter[1]);

                if (empty($val)) {
                    $val = 'no label given';
                }
            }

            return 'data-' . $key . '="' . $val . '"';
        }, array_keys($trackerInfo)));

        return $str;
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

        $dType = File::getFileExtension($download->getFilename());
        $downloadTitle = $showFileNameIfTitleEmpty ? $download->getFilename() : $this->translator->trans('Download', [], 'admin');
        $dName = ($download->getMetadata('title')) ?: $downloadTitle;
        $dAltText = $download->getMetadata('alt') ?: '';
        $dImageAltText = !empty($dAltText) ? $dAltText : $dName;

        $dPreviewImage = null;
        $previewThumbName = $this->configManager->getImageThumbnailFromConfig('download_preview_thumbnail');

        if ($showPreviewImage) {
            $metaPreviewImage = $download->getMetadata('previewImage');
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
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2);
            $format = 'gb';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2);
            $format = 'mb';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2);
            $format = 'kb';
        } elseif ($bytes > 1) {
            $format = 'bytes';
        } elseif ($bytes === 1) {
            $format = 'byte';
        } else {
            $format = 'bytes';
        }

        return round((float) $bytes, $precision) . ' ' . $format;
    }
}
