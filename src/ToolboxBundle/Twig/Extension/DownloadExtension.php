<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Asset;
use ToolboxBundle\Connector\BundleConnector;
use ToolboxBundle\Service\ConfigManager;
use Pimcore\Translation\Translator;

class DownloadExtension extends \Twig_Extension
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var BundleConnector
     */
    protected $bundleConnector;

    /**
     * @var \Pimcore\Translation\Translator
     */
    protected $translator;

    /**
     * AreaBlockConfigExtension constructor.
     *
     * @param ConfigManager   $configManager
     * @param BundleConnector $bundleConnector
     * @param Translator      $translator
     */
    public function __construct(ConfigManager $configManager, BundleConnector $bundleConnector, Translator $translator)
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
            new \Twig_Function('toolbox_download_info', [$this, 'getDownloadInfo']),
            new \Twig_Function('toolbox_download_tracker',
                [$this, 'getDownloadTracker'], ['is_safe' => ['html']]
            )
        ];
    }

    /**
     * @param string|array $areaType toolbox element or custom config
     * @param null|object  $element  related element to track
     *
     * @return string
     */
    public function getDownloadTracker($areaType, $element = NULL)
    {
        if (empty($areaType)) {
            return '';
        }

        if (is_array($areaType)) {
            $trackerInfo = $areaType;
        } else {
            $configNode = $this->configManager->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig($areaType);

            if (empty($configNode) || !isset($configNode['eventTracker'])) {
                return '';
            }

            $trackerInfo = $configNode['eventTracker'];
        }

        $str = 'data-tracking="active" ';

        $str .= join(' ', array_map(function ($key) use ($trackerInfo, $element) {
            $val = $trackerInfo[$key];

            if (is_bool($val)) {
                $val = (int)$val;
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
     * @param \Pimcore\Model\Asset $download
     * @param bool                 $showPreviewImage
     * @param string               $fileSizeUnit
     * @param int                  $fileSizePrecision
     *
     * @return array
     */
    public function getDownloadInfo($download, $showPreviewImage = FALSE, $fileSizeUnit = 'mb', $fileSizePrecision = 0)
    {
        if ($this->bundleConnector->hasBundle('MembersBundle\MembersBundle') === TRUE
            && strpos($download->getFullPath(), \MembersBundle\Security\RestrictionUri::PROTECTED_ASSET_FOLDER) !== FALSE
        ) {
            $dPath = $this->bundleConnector->getBundleService('members.security.restriction.uri')->generateAssetUrl($download);
        } else {
            $dPath = $download->getFullPath();
        }

        $dSize = $download->getFileSize($fileSizeUnit, $fileSizePrecision);
        $dType = \Pimcore\File::getFileExtension($download->getFilename());
        $dName = ($download->getMetadata('title')) ? $download->getMetadata('title') : $this->translator->trans('Download', [], 'admin');
        $dAltText = $download->getMetadata('alt') ? $download->getMetadata('alt') : $dName;

        $dPreviewImage = NULL;

        $previewThumbName = $this->configManager->getImageThumbnailFromConfig('download_preview_thumbnail');

        if ($showPreviewImage) {
            $dPreviewImage = $download->getMetadata('previewImage') instanceof Asset\Image
                ? $download->getMetadata('previewImage')->getThumbnail($previewThumbName)
                : (
                    $download instanceof Asset\Image
                    ? $download->getThumbnail($previewThumbName)
                    : ($download instanceof Asset\Document
                        ? $download->getImageThumbnail($previewThumbName)
                        : NULL)
                );
        }

        $dPreviewImagePath = NULL;
        $hasPreviewImage = FALSE;

        if ($dPreviewImage instanceof Asset\Image\Thumbnail) {
            $dPreviewImagePath = $dPreviewImage->getPath();
            $hasPreviewImage = TRUE;
        } else if ($dPreviewImage instanceof Asset\Document\ImageThumbnail && !empty($dPreviewImage->getConfig())) {
            $dPreviewImagePath = $dPreviewImage->getPath();
            $hasPreviewImage = TRUE;
        }

        return [
            'path'             => $dPath,
            'size'             => $dSize,
            'type'             => $dType,
            'name'             => $dName,
            'altText'          => $dAltText,
            'previewImage'     => $dPreviewImage,
            'hasPreviewImage'  => $hasPreviewImage,
            'previewImagePath' => $dPreviewImagePath
        ];
    }
}