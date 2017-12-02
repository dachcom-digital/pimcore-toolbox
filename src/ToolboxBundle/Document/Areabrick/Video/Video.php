<?php

namespace ToolboxBundle\Document\Areabrick\Video;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Model\Asset;

class Video extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        /** @var \ToolboxBundle\Model\Document\Tag\Vhs $videoTag */
        $videoTag = $this->getDocumentTag($info->getDocument(),'vhs', 'video');

        $playInLightBox = $videoTag->getShowAsLightbox() === TRUE ? 'true' : 'false';
        $autoPlay = $this->getDocumentTag($info->getDocument(),'checkbox', 'autoplay')->isChecked() === TRUE && !$view->get('editmode');
        $videoType = $videoTag->getVideoType();
        $posterPath = NULL;
        $imageThumbnail = NULL;
        $poster = $videoTag->getPosterAsset();
        $videoId = $videoTag->id;

        if ($poster instanceof Asset\Image) {
            $imageThumbnail = $this->getConfigManager()->getImageThumbnailFromConfig('video_poster');
            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $view->autoPlay       = $autoPlay;
        $view->posterPath     = $posterPath;
        $view->videoType      = $videoType;
        $view->playInLightbox = $playInLightBox;
        $view->videoId        = $videoId;
    }

    public function getName()
    {
        return 'Video';
    }

    public function getDescription()
    {
        return 'Toolbox Video';
    }
}