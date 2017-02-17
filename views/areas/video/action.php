<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class Video extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $playInLightBox = $this->view->vhs('video')->getShowAsLightbox() === TRUE ? 'true' : 'false';
        $autoPlay = $this->view->checkbox('autoplay')->isChecked() === '1' && !$this->view->editmode;
        $videoType = $this->view->vhs('video')->getVideoType();
        $posterPath = NULL;
        $imageThumbnail = NULL;
        $poster = $this->view->vhs('video')->getPosterAsset();

        if ($poster instanceof \Pimcore\Model\Asset\Image) {
            $configNode = \Toolbox\Config::getConfig()->video->toArray();
            if (isset($configNode['videoOptions'])) {
                if (isset($configNode['videoOptions'][$videoType])) {
                    $options = $configNode['videoOptions'][$videoType];
                    $imageThumbnail = isset($options['posterImageThumbnail']) ? $options['posterImageThumbnail'] : NULL;
                }
            }

            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $this->view->assign(
            [
                'autoPlay'       => $autoPlay,
                'posterPath'     => $posterPath,
                'videoType'      => $videoType,
                'playInLightbox' => $playInLightBox
            ]
        );
    }

    public function getBrickHtmlTagOpen($brick)
    {
        return '';
    }

    public function getBrickHtmlTagClose($brick)
    {
        return '';
    }
}