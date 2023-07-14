<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Image;
use Pimcore\Model\Document\Editable\Link;
use Pimcore\Model\Document\Editable\Select;
use Pimcore\Tests\Util\TestHelper;

class ImageTest extends AbstractAreaTest
{
    public const TYPE = 'image';

    public function testIframeBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(3, $configElements);
        $this->assertEquals('link', $configElements[0]['type']);
        $this->assertEquals('checkbox', $configElements[1]['type']);
        $this->assertEquals('checkbox', $configElements[2]['type']);
    }

    public function testImage()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $imageTag = new Image();
        $imageTag->setImage($asset);
        $imageTag->setText('caption');

        $elements = [
            'ci' => $imageTag
        ];

        $this->assertEquals(
            $this->filter($this->getCompareDefault($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testImageWithCaption()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompareCaption($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );

    }

    public function testImageWithLightBox()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompareLightBox($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testImageWithLink()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompareLink($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testImageWithAdditionalClass()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompareWithAdditionalClass($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompareDefault(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                ' . $asset->getThumbnail('contentImage')->getHtml(['imgAttributes' => ['class' => 'img-fluid']]) . '
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareCaption(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                ' . $asset->getThumbnail('contentImage')->getHtml(['imgAttributes' => ['class' => 'img-fluid']]) . '
                                <span class="caption">caption</span>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareLightBox(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div class="light-box">
                                <a href="' . $asset->getThumbnail('lightBoxImage')->getPath() . '" class="item">
                                    ' . $asset->getThumbnail('contentImage')->getHtml(['imgAttributes' => ['class' => 'img-fluid']]) . '
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareLink(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-image ">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                <a href="/test/test2" target="">
                                    ' . $asset->getThumbnail('contentImage')->getHtml(['imgAttributes' => ['class' => 'img-fluid']]) . '
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-image additional-class">
                    <div class="row">
                        <div class="col-12">
                            <div >
                                ' . $asset->getThumbnail('contentImage')->getHtml(['imgAttributes' => ['class' => 'img-fluid']]) . '
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
