<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Asset;
use ToolboxBundle\Connector\BundleConnector;
use Pimcore\Translation\Translator;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DownloadExtension extends AbstractExtension
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @var BundleConnector
     */
    protected $bundleConnector;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param ConfigManagerInterface $configManager
     * @param BundleConnector        $bundleConnector
     * @param Translator             $translator
     */
    public function __construct(ConfigManagerInterface $configManager, BundleConnector $bundleConnector, Translator $translator)
    {
        $this->configManager = $configManager;
        $this->bundleConnector = $bundleConnector;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
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
     * @param string|array $areaType toolbox element or custom config
     * @param null|object  $element  related element to track
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getDownloadTracker($areaType, $element = null)
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

        $str .= join(' ', array_map(function ($key) use ($trackerInfo, $element) {
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
     * @param Asset  $download
     * @param bool   $showPreviewImage
     * @param string $fileSizeUnit
     * @param int    $fileSizePrecision
     * @param bool   $showFileNameIfTitleEmpty
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDownloadInfo($download, $showPreviewImage = false, $fileSizeUnit = 'optimized', $fileSizePrecision = 0, $showFileNameIfTitleEmpty = false)
    {
        if ($this->bundleConnector->hasBundle('MembersBundle\MembersBundle') === true
            && strpos($download->getFullPath(), \MembersBundle\Security\RestrictionUri::PROTECTED_ASSET_FOLDER) !== false
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

        $dType = \Pimcore\File::getFileExtension($download->getFilename());
        $downloadTitle = $showFileNameIfTitleEmpty ? $download->getFilename() : $this->translator->trans('Download', [], 'admin');
        $dName = ($download->getMetadata('title')) ? $download->getMetadata('title') : $downloadTitle;
        $dAltText = $download->getMetadata('alt') ? $download->getMetadata('alt') : '';
        $dImageAltText = !empty($dAltText) ? $dAltText : $dName;

        $dPreviewImage = null;
        $previewThumbName = $this->configManager->getImageThumbnailFromConfig('download_preview_thumbnail');

        if ($showPreviewImage) {
            $dPreviewImage = $download->getMetadata('previewImage') instanceof Asset\Image
                ? $download->getMetadata('previewImage')->getThumbnail($previewThumbName)
                : (
                $download instanceof Asset\Image
                    ? $download->getThumbnail($previewThumbName)
                    : ($download instanceof Asset\Document
                    ? $download->getImageThumbnail($previewThumbName)
                    : null)
                );
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

    /**
     * Get optimized file size.
     *
     * @param int $bytes
     * @param int $precision
     *
     * @return string
     */
    public function getOptimizedFileSize($bytes, $precision)
    {
        $format = '';

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
        } elseif ($bytes == 1) {
            $format = 'byte';
        } else {
            $bytes = '0 bytes';
        }

        return round($bytes, $precision) . ' ' . $format;
    }
}
