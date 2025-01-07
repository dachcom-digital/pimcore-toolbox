<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Image;
use Pimcore\Model\Document\Editable\Input;
use Pimcore\Model\Document\Editable\Link;
use Pimcore\Model\Document\Editable\Select;
use Pimcore\Model\Document\Editable\Wysiwyg;
use Pimcore\Tests\Support\Util\TestHelper;

class TeaserTest extends AbstractAreaTest
{
    public const TYPE = 'teaser';

    public function testTeaserBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(3, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('type', $configElements[0]['name']);

        $this->assertEquals('select', $configElements[1]['type']);
        $this->assertEquals('layout', $configElements[1]['name']);

        $this->assertEquals('checkbox', $configElements[2]['type']);
        $this->assertEquals('use_light_box', $configElements[2]['name']);
    }

    public function testTeaser()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);
        $elements = $this->getDefaultElements($asset);

        $this->assertEquals(
            $this->filter($this->getCompare($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testTeaserWithLightBox()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);
        $elements = $this->getDefaultElements($asset);

        $lightBox = new Checkbox();
        $lightBox->setDataFromEditmode(1);

        $elements['use_light_box'] = $lightBox;

        $this->assertEquals(
            $this->filter($this->getCompareWithLightBox($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testTeaserWithAdditionalClass()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);
        $elements = $this->getDefaultElements($asset);

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements['add_classes'] = $combo;

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getDefaultElements($asset)
    {
        $type = new Select();
        $type->setDataFromResource('direct');

        $layout = new Select();
        $layout->setDataFromResource('default');

        $link = new Link();
        $link->setDataFromResource(serialize(['path' => '/test/test2', 'linktype' => 'direct', 'text' => '']));

        $image = new Image();
        $image->setDataFromEditmode([
            'id'  => $asset->getId(),
            'alt' => ''
        ]);

        $text = new Wysiwyg();
        $text->setDataFromEditmode('teaser text');

        $headline = new Input();
        $headline->setDataFromEditmode('teaser headline');

        return [
            'type'     => $type,
            'layout'   => $layout,
            'link'     => $link,
            'image'    => $image,
            'text'     => $text,
            'headline' => $headline
        ];
    }

    private function getCompare(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-teaser ">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default ">
                                <a href="/test/test2"  class="item">
                                    ' . $asset->getThumbnail('standardTeaser')->getHtml() . '
                                </a>
                                <h3 class="teaser-headline">teaser headline</h3>
                                <div class="teaser-text">        teaser text    </div>
                                <a href="/test/test2" path="/test/test2" linktype="direct" class="btn btn-default teaser-link"></a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithLightBox(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-teaser ">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default light-box">
                                <a href="' . $asset->getThumbnail('lightBoxImage')->getPath() . '" class="item">
                                    ' . $asset->getThumbnail('standardTeaser')->getHtml() . '
                                </a>
                                <h3 class="teaser-headline">teaser headline</h3><div class="teaser-text">        teaser text    </div>
                                <a href="/test/test2" path="/test/test2" linktype="direct" class="btn btn-default teaser-link"></a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass(\Pimcore\Model\Asset\Image $asset)
    {
        return '<div class="toolbox-element toolbox-teaser additional-class">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default ">
                                <a href="/test/test2"  class="item">
                                    ' . $asset->getThumbnail('standardTeaser')->getHtml() . '
                                </a>
                                <h3 class="teaser-headline">teaser headline</h3>
                                <div class="teaser-text">        teaser text    </div>
                                <a href="/test/test2" path="/test/test2" linktype="direct" class="btn btn-default teaser-link"></a>
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
