<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Image;
use Pimcore\Model\Document\Tag\Link;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Tests\Util\TestHelper;

class ImageTest extends AbstractAreaTest
{
    public function testImage()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $imageTag = new Image();
        $imageTag->setImage($asset);
        $imageTag->setText('caption');

        $elements = [
            'ci' => $imageTag
        ];

        $this->assertEquals(
            $this->filter($this->getCompareDefault($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('image', $elements))
        );
    }

    public function testImageWithCaption()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $imageTag = new Image();
        $imageTag->setImage($asset);
        $imageTag->setText('caption');

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'ci'           => $imageTag,
            'show_caption' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareCaption($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('image', $elements))
        );

    }

    public function testImageWithLightBox()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $imageTag = new Image();
        $imageTag->setImage($asset);
        $imageTag->setText('caption');

        $lightBox = new Checkbox();
        $lightBox->setDataFromResource(1);

        $elements = [
            'ci'            => $imageTag,
            'use_light_box' => $lightBox,
        ];

        $this->assertEquals(
            $this->filter($this->getCompareLightBox($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('image', $elements))
        );
    }

    public function testImageWithLink()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $imageTag = new Image();
        $imageTag->setImage($asset);
        $imageTag->setText('caption');

        $link = new Link();
        $link->setDataFromResource(['path' => '/test/test2']);

        $elements = [
            'ci'         => $imageTag,
            'image_link' => $link,
        ];

        $this->assertEquals(
            $this->filter($this->getCompareLink($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('image', $elements))
        );
    }

    public function testImageWithAdditionalClass()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $imageTag = new Image();
        $imageTag->setImage($asset);
        $imageTag->setText('caption');

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'ci'          => $imageTag,
            'add_classes' => $combo,
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('image', $elements))
        );
    }

    private function getCompareDefault($fileName)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                <img alt="" src="' . $fileName . '" />
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareCaption($fileName)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                <img alt="" src="' . $fileName . '" />
                                <span class="caption">caption</span>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareLightBox($fileName)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div class="light-box">
                                <a href="' . $fileName . '" class="item">
                                    <img alt="" src="' . $fileName . '" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareLink($fileName)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                <a href="/test/test2" target="">
                                    <img alt="" src="' . $fileName . '" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass($fileName)
    {
        return '<div class="toolbox-element toolbox-image additional-class">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                <img alt="" src="' . $fileName . '" />
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
