<?php

namespace ToolboxBundle\Service;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail\Config;
use Pimcore\Model\Document\Editable;
use ToolboxBundle\Manager\ConfigManagerInterface;

class AssetService
{
    public function __construct(protected ConfigManagerInterface $configManager)
    {
    }

    public function generateImageThumbnailFromEditable(Editable\Image $image, ?string $thumbnailName, array $thumbnailOptions = []): array
    {
        $thumbnail = $image->getThumbnail($thumbnailName);

        if ($thumbnail === '') {
            $thumbnail = null;
        }

        return $this->buildAssetData($image->getImage(), $thumbnail, $thumbnailOptions);
    }

    public function generateImageThumbnail(Asset\Image $asset, ?string $thumbnailName, array $thumbnailOptions = []): array
    {
        $thumbnail = $asset->getThumbnail($thumbnailName);

        return $this->buildAssetData($asset, $thumbnail, $thumbnailOptions);
    }

    private function buildAssetData(?Asset\Image $asset, ?Image\Thumbnail $thumbnail, array $options): array
    {
        if (!$asset instanceof Asset\Image) {
            return [];
        }

        $title = $asset->getMetadata('title');
        $description = $asset->getMetadata('description');
        $copyright = $asset->getMetadata('copyright');

        return [
            'title'                 => $title,
            'description'           => $description,
            'copyright'             => $copyright,
            'markup'                => $thumbnail?->getHtml($options),
            'mediaList'             => $thumbnail === null ? null : $this->parseThumbnailPictureList($thumbnail, $options),
            'path'                  => $thumbnail?->getFrontendPath(),
            'lowQualityPlaceholder' => $this->parseLowQualityPlaceholder($asset),
        ];
    }

    private function parseLowQualityPlaceholder(Asset\Image $asset): string
    {
        $emptyGif = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        return $asset->getLowQualityPreviewDataUri() ?: $emptyGif;
    }

    private function parseThumbnailPictureList(Asset\Image\Thumbnail $thumbnail, array $thumbnailOptions): array
    {
        $image = $thumbnail->getAsset();
        $thumbConfig = $thumbnail->getConfig();
        $mediaConfigs = $thumbConfig->getMedias();

        $isAutoFormat = $thumbConfig instanceof Config && strtolower($thumbConfig->getFormat()) === 'source';

        ksort($mediaConfigs, SORT_NUMERIC);
        $mediaConfigs[] = $thumbConfig->getItems();

        $data = [];

        foreach ($mediaConfigs as $mediaQuery => $config) {

            $thumbConfig->setItems($config);
            $sourceAttributes = $this->getSourceTagAttributes($thumbnail, $thumbConfig, $mediaQuery, $image, $thumbnailOptions);

            if (empty($sourceAttributes)) {
                continue;
            }

            if (!$isAutoFormat) {
                continue;
            }

            foreach ($thumbConfig->getAutoFormatThumbnailConfigs() as $autoFormatConfig) {
                $autoFormatThumbnailAttributes = $this->getSourceTagAttributes($thumbnail, $autoFormatConfig, $mediaQuery, $image, $thumbnailOptions);
                if (!empty($autoFormatThumbnailAttributes)) {
                    $data[] = $autoFormatThumbnailAttributes;
                }
            }

            $data[] = $sourceAttributes;
        }

        return $data;
    }

    private function getSourceTagAttributes(Asset\Image\Thumbnail $thumbnail, Config $thumbConfig, mixed $mediaQuery, Image $image, array $options): array
    {
        $sourceTagAttributes = [];
        $sourceTagAttributes['srcset'] = $this->getSrcset($thumbnail->getAsset()->getFilename(), $thumbConfig, $image, $mediaQuery);
        $thumb = $image->getThumbnail($thumbConfig, true);

        if ($mediaQuery) {
            $sourceTagAttributes['media'] = $mediaQuery;
            $thumb->reset();
        }

        if (isset($options['previewDataUri'])) {
            $sourceTagAttributes['data-srcset'] = $sourceTagAttributes['srcset'];
            unset($sourceTagAttributes['srcset']);
        }

        if (!isset($options['disableWidthHeightAttributes'])) {
            if ($thumb->getWidth()) {
                $sourceTagAttributes['width'] = $thumb->getWidth();
            }

            if ($thumb->getHeight()) {
                $sourceTagAttributes['height'] = $thumb->getHeight();
            }
        }

        $sourceTagAttributes['type'] = $thumb->getMimeType();

        $sourceCallback = $options['sourceCallback'] ?? null;
        if ($sourceCallback) {
            $sourceTagAttributes = $sourceCallback($sourceTagAttributes);
        }

        return $sourceTagAttributes;
    }

    private function getSrcset(string $fileName, Config $thumbConfig, Image $image, mixed $mediaQuery = null): string
    {
        $useOriginalFile = !$thumbConfig->isRasterizeSVG() && preg_match("@\.svgz?$@", $fileName);

        $srcSetValues = [];
        foreach ([1, 2] as $highRes) {

            $thumbConfigRes = clone $thumbConfig;

            if ($mediaQuery) {
                $thumbConfigRes->selectMedia($mediaQuery);
            }

            $thumbConfigRes->setHighResolution($highRes);
            $thumb = $image->getThumbnail($thumbConfigRes, true)->getPath();

            $descriptor = $highRes . 'x';
            // encode comma in thumbnail path as srcset is a comma separated list
            $srcSetValues[] = str_replace(',', '%2C', $thumb . ' ' . $descriptor);

            if ($useOriginalFile && $thumbConfig->isSvgTargetFormatPossible()) {
                break;
            }
        }

        return implode(', ', $srcSetValues);
    }
}
