<?php

namespace ToolboxBundle\Document\Areabrick\Video;

use Pimcore\Model\Document\Editable\Checkbox;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Asset;
use ToolboxBundle\Model\Document\Editable\Vhs;

class Video extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        parent::action($info);

        /** @var Vhs $videoTag */
        $videoTag = $this->getDocumentEditable($info->getDocument(), 'vhs', 'video');

        $videoParameter = $videoTag->getVideoParameter();

        $playInLightBox = $videoTag->getShowAsLightBox() === true ? 'true' : 'false';
        /** @var Checkbox $autoPlayElement */
        $autoPlayElement = $this->getDocumentEditable($info->getDocument(), 'checkbox', 'autoplay');
        $autoPlay = $autoPlayElement->isChecked() === true && !$info->getEditable()->getEditmode();
        $videoType = $videoTag->getVideoType();
        $posterPath = null;
        $poster = $videoTag->getPosterAsset();

        if ($poster instanceof Asset\Image) {
            $imageThumbnail = $this->getConfigManager()->getImageThumbnailFromConfig('video_poster');
            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $info->setParams(array_merge($info->getParams(), [
            'autoPlay'       => $autoPlay,
            'posterPath'     => $posterPath,
            'videoType'      => $videoType,
            'playInLightbox' => $playInLightBox,
            'videoParameter' => $videoParameter,
            'videoId'        => $videoTag->getId()
        ]));

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Video';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Video';
    }
}
