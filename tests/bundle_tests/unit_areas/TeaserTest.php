<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Image;
use Pimcore\Model\Document\Tag\Input;
use Pimcore\Model\Document\Tag\Link;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Model\Document\Tag\Wysiwyg;
use Pimcore\Tests\Util\TestHelper;

class TeaserTest extends AbstractAreaTest
{
    private function getDefaultElements($asset)
    {
        $type = new Select();
        $type->setDataFromResource('direct');

        $layout = new Select();
        $layout->setDataFromResource('default');

        $link = new Link();
        $link->setDataFromResource(['path' => '/test/test2']);

        $image = new Image();
        $image->setDataFromEditmode([
            'id'  => $asset->getId(),
            'alt' => 'alt text'
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

    public function testTeaser()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);
        $elements = $this->getDefaultElements($asset);

        $this->assertEquals(
            $this->filter($this->getCompare($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('teaser', $elements))
        );
    }

    public function testTeaserWithLightBox()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);
        $elements = $this->getDefaultElements($asset);

        $lightBox = new Checkbox();
        $lightBox->setDataFromEditmode(1);

        $elements['use_light_box'] = $lightBox;

        $this->assertEquals(
            $this->filter($this->getCompareWithLightBox($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('teaser', $elements))
        );
    }

    public function testTeaserWithAdditionalClass()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);
        $elements = $this->getDefaultElements($asset);

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements['add_classes'] = $combo;

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('teaser', $elements))
        );
    }

    private function getCompare($asset)
    {
        return '<div class="toolbox-element toolbox-teaser ">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default ">
                                <a href="/test/test2"  class="item">
                                    <img class="img-responsive" alt="alt text" title="alt text" src="' . $asset . '" />
                                </a>
                                <h3 class="teaser-headline">teaser headline</h3>
                                <div class="teaser-text">        teaser text    </div>
                                <a href="/test/test2" class="btn btn-default teaser-link"></a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithLightBox($asset)
    {
        return '<div class="toolbox-element toolbox-teaser ">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default light-box">
                                <a href="' . $asset . '" class="item">
                                    <img class="img-responsive" alt="alt text" title="alt text" src="' . $asset . '" />
                                </a>
                                <h3 class="teaser-headline">teaser headline</h3><div class="teaser-text">        teaser text    </div>
                                <a href="/test/test2" class="btn btn-default teaser-link"></a>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass($asset)
    {
        return '<div class="toolbox-element toolbox-teaser additional-class">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default ">
                                <a href="/test/test2"  class="item">
                                    <img class="img-responsive" alt="alt text" title="alt text" src="' . $asset . '" />
                                </a>
                                <h3 class="teaser-headline">teaser headline</h3>
                                <div class="teaser-text">        teaser text    </div>
                                <a href="/test/test2" class="btn btn-default teaser-link"></a>
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
