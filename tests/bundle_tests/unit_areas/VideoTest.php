<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Tests\Util\TestHelper;
use ToolboxBundle\Model\Document\Tag\Vhs;

class VideoTest extends AbstractAreaTest
{
    public function testYoutubeVideo()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'   => 'youtube',
            'path'   => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster' => $asset->getFullPath()
        ]);

        $elements = [
            'video' => $video
        ];

        $this->assertEquals(
            $this->filter($this->getCompare($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('video', $elements))
        );
    }

    public function testVimeoVideo()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'   => 'vimeo',
            'path'   => 'https://vimeo.com/76979871',
            'poster' => $asset->getFullPath()
        ]);

        $elements = [
            'video' => $video
        ];

        $this->assertEquals(
            $this->filter($this->getCompareVimeo($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('video', $elements))
        );
    }
    
    public function testVideoWithLightBox()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'path'           => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster'         => $asset->getFullPath(),
            'showAsLightbox' => true
        ]);

        $elements = [
            'video' => $video
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithLightBox($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('video', $elements))
        );
    }

    public function testVideoWithAutoplay()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'path'           => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster'         => $asset->getFullPath(),
            'showAsLightbox' => true
        ]);

        $autoplay = new Checkbox();
        $autoplay->setDataFromEditmode(1);

        $elements = [
            'video'    => $video,
            'autoplay' => $autoplay
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAutoplay($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('video', $elements))
        );
    }

    public function testVideoWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $asset = TestHelper::createImageAsset('', true);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'   => 'youtube',
            'id'     => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster' => $asset->getFullPath()
        ]);

        $elements = [
            'video'       => $video,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('video', $elements))
        );
    }

    private function getCompare($path)
    {
        return '<div class="toolbox-element toolbox-video  " data-type="youtube">
                    <div class="video-inner">
                        <div class="player" data-poster-path="' . $path . '" data-play-in-lightbox="false" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $path . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareVimeo($path)
    {
        return '<div class="toolbox-element toolbox-video  " data-type="vimeo">
                    <div class="video-inner">
                        <div class="player" data-poster-path="' . $path . '" data-play-in-lightbox="false" data-video-uri="https://vimeo.com/76979871"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $path . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithLightBox($path)
    {
        return '<div class="toolbox-element toolbox-video  " data-type="youtube">
                    <div class="video-inner">
                        <div class="player" data-poster-path="' . $path . '" data-play-in-lightbox="true" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $path . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAutoplay($path)
    {
        return '<div class="toolbox-element toolbox-video   autoplay" data-type="youtube">
                    <div class="video-inner">
                        <div class="player" data-poster-path="' . $path . '" data-play-in-lightbox="true" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $path . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass($path)
    {
        return '<div class="toolbox-element toolbox-video additional-class " data-type="youtube">
                    <div class="video-inner">
                        <div class="player" data-poster-path="' . $path . '" data-play-in-lightbox="false" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $path . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }
}
