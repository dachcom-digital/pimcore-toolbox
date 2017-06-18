<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Service\ConfigManager;
use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Pimcore\Translation\Translator;

class DownloadExtension extends \Twig_Extension
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var PimcoreBundleManager
     */
    protected $bundleManager;

    /**
     * @var \Pimcore\Translation\Translator
     */
    protected $translator;

    /**
     * AreaBlockConfigExtension constructor.
     *
     * @param ConfigManager        $configManager
     * @param PimcoreBundleManager $bundleManager
     * @param Translator $translator
     */
    public function __construct(ConfigManager $configManager, PimcoreBundleManager $bundleManager, Translator $translator)
    {
        $this->configManager = $configManager;
        $this->bundleManager = $bundleManager;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('toolbox_download_info', [$this, 'getDownloadInfo']),
            new \Twig_SimpleFunction('toolbox_download_tracker',
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

        if (is_array($areaType))  //custom data
        {
            $trackerInfo = $areaType;
        } else //area data
        {
            $configNode = $this->configManager->getAreaParameterConfig($areaType);

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
     * @todo: fix members binding
     *
     * @param \Pimcore\Model\Asset $download
     * @param bool                 $showPreviewImage
     * @param bool                 $showFileInfo
     * @param string               $fileSizeUnit
     *
     * @return array
     */
    public function getDownloadInfo($download, $showPreviewImage = FALSE, $showFileInfo = FALSE, $fileSizeUnit = 'mb')
    {
        if ($this->hasMembersExtension() === TRUE && strpos($download->getFullPath(), \Members\Tool\UrlServant::PROTECTED_ASSET_FOLDER) !== FALSE) {
            $dPath = \Members\Tool\UrlServant::generateAssetUrl($download);
        } else {
            $dPath = $download->getFullPath();
        }

        $dSize = $download->getFileSize($fileSizeUnit, 2);
        $dType = \Pimcore\File::getFileExtension($download->getFilename());
        $dName = ($download->getMetadata('title')) ? $download->getMetadata('title') : $this->translator->trans('Download', [], 'admin');
        $dAltText = $download->getMetadata('alt') ? $download->getMetadata('alt') : $dName;
        $dPreviewImage = NULL;

        if ($showPreviewImage) {
            $dPreviewImage = $download->getMetadata('previewImage') instanceof \Pimcore\Model\Asset\Image
                ? $download->getMetadata('previewImage')->getThumbnail('downloadPreviewImage')
                : (
                $download instanceof \Pimcore\Model\Asset\Image
                    ? $download->getThumbnail('downloadPreviewImage')
                    : $download->getImageThumbnail('downloadPreviewImage')
                );
        }

        return [
            'path'         => $dPath,
            'size'         => $dSize,
            'type'         => $dType,
            'name'         => $dName,
            'altText'      => $dAltText,
            'previewImage' => $dPreviewImage
        ];
    }

    private function hasMembersExtension()
    {
        $hasMembers = FALSE;

        try {
            $hasMembers = $this->bundleManager->isEnabled('MembersBundle\MembersBundle');
        } catch (\Exception $e) {
        }

        return $hasMembers;
    }
}