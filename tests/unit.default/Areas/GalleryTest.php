<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Relations;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Tests\Util\TestHelper;

class GalleryTest extends AbstractAreaTest
{
    const TYPE = 'gallery';

    public function testGalleryBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(3, $configElements);
        $this->assertEquals('relations', $configElements[0]['additional_config']['type']);
        $this->assertEquals('images', $configElements[0]['additional_config']['name']);

        $this->assertEquals('checkbox', $configElements[1]['additional_config']['type']);
        $this->assertEquals('use_light_box', $configElements[1]['additional_config']['name']);

        $this->assertEquals('checkbox', $configElements[2]['additional_config']['type']);
        $this->assertEquals('use_thumbnails', $configElements[2]['additional_config']['name']);
    }

    public function testGallery()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $images = new Relations();
        $images->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $elements = [
            'images' => $images
        ];

        $this->assertEquals(
            $this->filter($this->getCompare($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    public function testGalleryWithLightBox()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $images = new Relations();
        $images->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'images'        => $images,
            'use_light_box' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithLightBox($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    public function testGalleryWithThumbnails()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $images = new Relations();
        $images->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'images'         => $images,
            'use_thumbnails' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithThumbnails($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    public function testGalleryWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $images = new Relations();
        $images->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $elements = [
            'images'      => $images,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset1, $asset2)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    private function getCompare($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-gallery ">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled  test-gal responsive-dots "                        data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item">
                                    <img alt="" src="' . $path1 . '" />
                                </li>
                                <li class="slide item">
                                    <img alt="" src="' . $path2 . '" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithLightBox($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-gallery ">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled  test-gal responsive-dots light-box"                        data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item"data-src="' . $path1 . '">
                                    <img alt="" src="' . $path1 . '" />
                                </li>
                                <li class="slide item"data-src="' . $path2 . '">
                                    <img alt="" src="' . $path2 . '" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithThumbnails($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-gallery ">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled thumbnail-slider test-gal responsive-dots "            data-as-nav-for=".test-thumbs"            data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item">
                                    <img alt="" src="' . $path1 . '" />
                                </li>
                                <li class="slide item">
                                    <img alt="" src="' . $path2 . '" />
                                </li>
                            </ul>
                            <ul class="slick-slider slick-slider-thumbs list-unstyled test-thumbs"            data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="5" data-dots="false" data-arrows="true" data-center-mode="true"            data-as-nav-for=".test-gal"        >
                                <li class="slide">
                                    <img alt="" src="' . $path1 . '" />
                                </li>
                                <li class="slide">
                                    <img alt="" src="' . $path2 . '" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-gallery additional-class">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled  test-gal responsive-dots "                        data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item">
                                    <img alt="" src="' . $path1 . '" />
                                </li>
                                <li class="slide item">
                                    <img alt="" src="' . $path2 . '" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }
}
