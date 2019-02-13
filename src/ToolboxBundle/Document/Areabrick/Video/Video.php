<?php

namespace ToolboxBundle\Document\Areabrick\Video;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Model\Asset;

class Video extends AbstractAreabrick
{
    /**
     * @param Info $info
     *
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        /** @var \ToolboxBundle\Model\Document\Tag\Vhs $videoTag */
        $videoTag = $this->getDocumentTag($info->getDocument(), 'vhs', 'video');

        $playInLightBox = $videoTag->getShowAsLightbox() === true ? 'true' : 'false';
        /** @var \Pimcore\Model\Document\Tag\Checkbox $autoPlayElement */
        $autoPlayElement = $this->getDocumentTag($info->getDocument(), 'checkbox', 'autoplay');
        $autoPlay = $autoPlayElement->isChecked() === true && !$view->get('editmode');
        $videoType = $videoTag->getVideoType();
        $posterPath = null;
        $imageThumbnail = null;
        $poster = $videoTag->getPosterAsset();
        $videoId = $videoTag->id;

        if ($poster instanceof Asset\Image) {
            $imageThumbnail = $this->getConfigManager()->getImageThumbnailFromConfig('video_poster');
            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $view->getParameters()->add([
            'autoPlay'       => $autoPlay,
            'posterPath'     => $posterPath,
            'videoType'      => $videoType,
            'playInLightbox' => $playInLightBox,
            'videoId'        => $videoId
        ]);
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
