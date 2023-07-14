<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Relations;
use Pimcore\Model\Document\Editable\Select;
use Pimcore\Tests\Util\TestHelper;

class GalleryTest extends AbstractAreaTest
{
    public const TYPE = 'gallery';

    public function testGalleryBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(3, $configElements);
        $this->assertEquals('relations', $configElements[0]['type']);
        $this->assertEquals('images', $configElements[0]['name']);

        $this->assertEquals('checkbox', $configElements[1]['type']);
        $this->assertEquals('use_light_box', $configElements[1]['name']);

        $this->assertEquals('checkbox', $configElements[2]['type']);
        $this->assertEquals('use_thumbnails', $configElements[2]['name']);
    }

    public function testGallery()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', null);
        $asset2 = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompare($asset1, $asset2)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    public function testGalleryWithLightBox()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', null);
        $asset2 = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompareWithLightBox($asset1, $asset2)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    public function testGalleryWithThumbnails()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', null);
        $asset2 = TestHelper::createImageAsset('', null);

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
            $this->filter($this->getCompareWithThumbnails($asset1, $asset2)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxGalleryId' => 'test']))
        );
    }

    public function testGalleryWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $asset1 = TestHelper::createImageAsset('', null);
        $asset2 = TestHelper::createImageAsset('', null);

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

    private function getCompare(\Pimcore\Model\Asset\Image $asset1, \Pimcore\Model\Asset\Image $asset2)
    {
        return '<div class="toolbox-element toolbox-gallery ">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled  test-gal responsive-dots "                        data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item">
                                    ' . $asset1->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                                <li class="slide item">
                                   ' . $asset2->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithLightBox(\Pimcore\Model\Asset\Image $asset1, \Pimcore\Model\Asset\Image $asset2)
    {
        return '<div class="toolbox-element toolbox-gallery ">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled  test-gal responsive-dots light-box"                        data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item"data-src="' . $asset1->getThumbnail('contentImage')->getPath() . '">
                                    ' . $asset1->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                                <li class="slide item"data-src="' . $asset2->getThumbnail('contentImage')->getPath() . '">
                                    ' . $asset2->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithThumbnails(\Pimcore\Model\Asset\Image $asset1, \Pimcore\Model\Asset\Image $asset2)
    {
        return '<div class="toolbox-element toolbox-gallery ">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled thumbnail-slider test-gal responsive-dots "            data-as-nav-for=".test-thumbs"            data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item">
                                    ' . $asset1->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                                <li class="slide item">
                                    ' . $asset2->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                            </ul>
                            <ul class="slick-slider slick-slider-thumbs list-unstyled test-thumbs"            data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="5" data-dots="false" data-arrows="true" data-center-mode="true"            data-as-nav-for=".test-gal"        >
                                <li class="slide">
                                    ' . $asset1->getThumbnail('galleryThumb')->getHtml() . '
                                </li>
                                <li class="slide">
                                    ' . $asset2->getThumbnail('galleryThumb')->getHtml() . '
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass(\Pimcore\Model\Asset\Image $asset1, \Pimcore\Model\Asset\Image $asset2)
    {
        return '<div class="toolbox-element toolbox-gallery additional-class">
                    <div class="row">
                        <div class="col-12 col-gallery">
                            <ul class="slick-slider list-unstyled  test-gal responsive-dots "                        data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true"    >
                                <li class="slide item">
                                    ' . $asset1->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                                <li class="slide item">
                                    ' . $asset2->getThumbnail('galleryImage')->getHtml() . '
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>';
    }
}
