<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;
use Pimcore\Model\Asset;

class Video extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $adminData = NULL;
        if ($this->getView()->editmode) {
            $adminData = \Toolbox\Tool\ElementBuilder::buildElementConfig('video', $this->getView());
        }

        $playInLightBox = $this->getView()->vhs('video')->getShowAsLightbox() === TRUE ? 'true' : 'false';
        $autoPlay = $this->getView()->checkbox('autoplay')->isChecked() === '1' && !$this->getView()->editmode;
        $videoType = $this->getView()->vhs('video')->getVideoType();
        $posterPath = NULL;
        $imageThumbnail = NULL;
        $poster = $this->getView()->vhs('video')->getPosterAsset();
        $videoId = $this->getView()->vhs('video')->id;

        if ($poster instanceof Asset\Image) {
            $configNode = \Toolbox\Config::getConfig()->video->toArray();
            if (isset($configNode['videoOptions'])) {
                if (isset($configNode['videoOptions'][$videoType])) {
                    $options = $configNode['videoOptions'][$videoType];
                    $imageThumbnail = isset($options['posterImageThumbnail']) ? $options['posterImageThumbnail'] : NULL;
                }
            }

            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $this->getView()->assign(
            [
                'adminData'      => $adminData,
                'autoPlay'       => $autoPlay,
                'posterPath'     => $posterPath,
                'videoType'      => $videoType,
                'playInLightbox' => $playInLightBox,
                'videoId'        => $videoId
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