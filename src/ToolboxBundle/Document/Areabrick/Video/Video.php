<?php

namespace ToolboxBundle\Document\Areabrick\Video;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Model\Asset;

class Video extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();
        $view->elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);

        /** @var \ToolboxBundle\Model\Document\Tag\Vhs $videoTag */
        $videoTag = $this->getDocumentTag($info->getDocument(),'vhs', 'video');

        $playInLightBox = $videoTag->getShowAsLightbox() === TRUE ? 'true' : 'false';
        $autoPlay = $this->getDocumentTag($info->getDocument(),'checkbox', 'autoplay')->isChecked() === '1' && !$this->getView()->editmode;
        $videoType = $videoTag->getVideoType();
        $posterPath = NULL;
        $imageThumbnail = NULL;
        $poster = $videoTag->getPosterAsset();
        $videoId = $videoTag->id;

        if ($poster instanceof Asset\Image) {

            $configNode = $this->getConfigManager()->getAreaParameterConfig('video');

            if (isset($configNode['videoTypes'])) {
                if (isset($configNode['videoTypes'][$videoType])) {
                    $options = $configNode['videoTypes'][$videoType];
                    $imageThumbnail = isset($options['posterImageThumbnail']) ? $options['posterImageThumbnail'] : NULL;
                }
            }

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