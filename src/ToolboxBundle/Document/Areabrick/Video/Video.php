<?php

namespace ToolboxBundle\Document\Areabrick\Video;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Asset;

class Video extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        /** @var \ToolboxBundle\Model\Document\Tag\Vhs $videoTag */
        $videoTag = $this->getDocumentEditable($info->getDocument(), 'vhs', 'video');

        $videoParameter = $videoTag->getVideoParameter();

        $playInLightBox = $videoTag->getShowAsLightBox() === true ? 'true' : 'false';
        /** @var \Pimcore\Model\Document\Editable\Checkbox $autoPlayElement */
        $autoPlayElement = $this->getDocumentEditable($info->getDocument(), 'checkbox', 'autoplay');
        $autoPlay = $autoPlayElement->isChecked() === true && !$info->getParam('editmode');
        $videoType = $videoTag->getVideoType();
        $posterPath = null;
        $imageThumbnail = null;
        $poster = $videoTag->getPosterAsset();
        $videoId = $videoTag->getId();

        if ($poster instanceof Asset\Image) {
            $imageThumbnail = $this->getConfigManager()->getImageThumbnailFromConfig('video_poster');
            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $info->setParams([
            'autoPlay'       => $autoPlay,
            'posterPath'     => $posterPath,
            'videoType'      => $videoType,
            'playInLightbox' => $playInLightBox,
            'videoParameter' => $videoParameter,
            'videoId'        => $videoId
        ]);

        return null;
    }

    public function getName(): string
    {
        return 'Video';
    }

    public function getDescription(): string
    {
        return 'Toolbox Video';
    }
}
