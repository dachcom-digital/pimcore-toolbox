<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Select;
use Pimcore\Tests\Util\TestHelper;
use ToolboxBundle\Model\Document\Editable\Vhs;

class VideoTest extends AbstractAreaTest
{
    public const TYPE = 'video';

    public function testVideoBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(1, $configElements);
        $this->assertEquals('checkbox', $configElements[0]['type']);
        $this->assertEquals('autoplay', $configElements[0]['name']);
    }

    public function testVideoConfigParameter()
    {
        $configParam = $this->getToolboxConfig()->getAreaParameterConfig('video');
        $this->assertEquals(
            [
                'video_types' => [
                    'asset'       => [
                        'active'         => false,
                        'allow_lightbox' => true
                    ],
                    'youtube'     => [
                        'active'         => true,
                        'allow_lightbox' => true
                    ],
                    'vimeo'       => [
                        'active'         => false,
                        'allow_lightbox' => true
                    ],
                    'dailymotion' => [
                        'active'         => false,
                        'allow_lightbox' => true
                    ]
                ]
            ],
            $configParam
        );
    }

    public function testYoutubeVideo()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'path'           => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'title'          => '',
            'description'    => '',
            'id'             => null,
            'videoParameter' => [],
            'showAsLightbox' => false,
            'poster'         => $asset->getFullPath()
        ]);

        $elements = [
            'video' => $video
        ];

        $this->assertEquals(
            $this->filter($this->getCompare($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testVimeoVideo()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'vimeo',
            'path'           => 'https://vimeo.com/76979871',
            'title'          => '',
            'description'    => '',
            'id'             => null,
            'videoParameter' => [],
            'showAsLightbox' => false,
            'poster'         => $asset->getFullPath()
        ]);

        $elements = [
            'video' => $video
        ];

        $this->assertEquals(
            $this->filter($this->getCompareVimeo($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testVideoWithLightBox()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'path'           => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster'         => $asset->getFullPath(),
            'title'          => '',
            'description'    => '',
            'videoParameter' => [],
            'id'             => null,
            'showAsLightbox' => true
        ]);

        $elements = [
            'video' => $video
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithLightBox($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testVideoWithAutoplay()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'path'           => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster'         => $asset->getFullPath(),
            'title'          => '',
            'description'    => '',
            'id'             => null,
            'videoParameter' => [],
            'showAsLightbox' => true
        ]);

        $autoplay = new Checkbox();
        $autoplay->setDataFromEditmode(1);

        $elements = [
            'video'    => $video,
            'autoplay' => $autoplay
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAutoplay($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testVideoWithVideoParameter()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $videoParameter = [
            ['key' => 'color', 'value' => 'red'],
            ['key' => 'rel', 'value' => '0']
        ];

        $parsedVideoParameters = [
            'color' => 'red',
            'rel'   => '0'
        ];

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'path'           => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'poster'         => $asset->getFullPath(),
            'showAsLightbox' => true,
            'title'          => '',
            'description'    => '',
            'id'             => null,
            'videoParameter' => $videoParameter
        ]);

        $autoplay = new Checkbox();
        $autoplay->setDataFromEditmode(1);

        $elements = [
            'video'    => $video,
            'autoplay' => $autoplay
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithVideoParameter($asset, $parsedVideoParameters)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testVideoWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $asset = TestHelper::createImageAsset('', null);

        $video = new Vhs();
        $video->setDataFromEditmode([
            'type'           => 'youtube',
            'id'             => 'https://www.youtube.com/watch?v=EhhGzxhtx48',
            'title'          => '',
            'description'    => '',
            'videoParameter' => [],
            'showAsLightbox' => false,
            'poster'         => $asset->getFullPath()
        ]);

        $elements = [
            'video'       => $video,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-video  " data-type="youtube">
                    <div class="video-inner">
                        <div class="player"  data-poster-path="' . $asset->getThumbnail('videoPoster')->getPath() . '" data-play-in-lightbox="false" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $asset->getThumbnail('videoPoster')->getPath() . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareVimeo(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-video  " data-type="vimeo">
                    <div class="video-inner">
                        <div class="player"  data-poster-path="' . $asset->getThumbnail('videoPoster')->getPath() . '" data-play-in-lightbox="false" data-video-uri="https://vimeo.com/76979871"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $asset->getThumbnail('videoPoster')->getPath() . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithLightBox(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-video  " data-type="youtube">
                    <div class="video-inner">
                        <div class="player"  data-poster-path="' . $asset->getThumbnail('videoPoster')->getPath() . '" data-play-in-lightbox="true" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $asset->getThumbnail('videoPoster')->getPath() . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAutoplay(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-video   autoplay" data-type="youtube">
                    <div class="video-inner">
                        <div class="player"  data-poster-path="' . $asset->getThumbnail('videoPoster')->getPath() . '" data-play-in-lightbox="true" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $asset->getThumbnail('videoPoster')->getPath() . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithVideoParameter(\Pimcore\Model\Asset\Image $asset, $attributes)
    {
        $safeAttributes = htmlspecialchars(json_encode($attributes));
        return '<div class="toolbox-element toolbox-video   autoplay" data-type="youtube">
                    <div class="video-inner">
                        <div class="player" data-video-parameter="' . $safeAttributes . '" data-poster-path="' . $asset->getThumbnail('videoPoster')->getPath() . '" data-play-in-lightbox="true" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $asset->getThumbnail('videoPoster')->getPath() . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-video additional-class " data-type="youtube">
                    <div class="video-inner">
                        <div class="player"  data-poster-path="' . $asset->getThumbnail('videoPoster')->getPath() . '" data-play-in-lightbox="false" data-video-uri="https://www.youtube.com/watch?v=EhhGzxhtx48"></div>
                        <div class="poster-overlay lightbox" style="background-image:url(\'' . $asset->getThumbnail('videoPoster')->getPath() . '\');">
                            <span class="icon"></span>
                        </div>
                    </div>
                </div>';
    }
}
