<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Block;
use Pimcore\Model\Document\Tag\Href;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Tests\Util\TestHelper;
use ToolboxBundle\Model\Document\Tag\ParallaxImage;

class ParallaxContainerTest extends AbstractAreaTest
{
    private function getDefaultElements($asset)
    {
        $backgroundImage = new Href();
        $backgroundImage->setDataFromEditmode([
            'id'   => $asset->getId(),
            'type' => 'asset'
        ]);

        $template = new Select();
        $template->setDataFromEditmode('no-template');

        $backgroundColor = new Select();
        $backgroundColor->setDataFromEditmode('default');

        $imageFront = new ParallaxImage();
        $imageFront->setDataFromEditmode([
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'parallaxPosition' => 'top-left',
                'parallaxSize'     => 'quarter-window-width'
            ],
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'parallaxPosition' => 'top-left',
                'parallaxSize'     => 'half-window-width'
            ]
        ]);

        $imageBehind = new ParallaxImage();
        $imageBehind->setDataFromEditmode([
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'parallaxPosition' => 'top-left',
                'parallaxSize'     => 'third-window-width'
            ],
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'parallaxPosition' => 'center-right',
                'parallaxSize'     => 'half-window-width'
            ]
        ]);

        $block = new Block();
        $block->setName('test-parallax-container-section');
        $block->setDataFromEditmode([1, 2]);

        $sectionTemplate = new Select();
        $sectionTemplate->setDataFromEditmode('no-template');

        $sectionContainerType = new Select();
        $sectionContainerType->setDataFromEditmode('container-fluid');

        $sectionBackgroundImage = new Href();
        $sectionBackgroundImage->setDataFromEditmode([
            'id'   => $asset->getId(),
            'type' => 'asset'
        ]);

        $sectionBackgroundColor = new Select();
        $sectionBackgroundColor->setDataFromEditmode('no-background-color');

        return [
            'template'               => $template,
            'background_image'       => $backgroundImage,
            'background_color'       => $backgroundColor,
            'image_front'            => $imageFront,
            'image_behind'           => $imageBehind,
            'pcB'                    => $block,
            'pcB:1.template'         => $sectionTemplate,
            'pcB:2.template'         => $sectionTemplate,
            'pcB:1.container_type'   => $sectionContainerType,
            'pcB:2.container_type'   => $sectionContainerType,
            'pcB:1.background_image' => $sectionBackgroundImage,
            'pcB:2.background_image' => $sectionBackgroundImage,
            'pcB:1.background_color' => $sectionBackgroundColor,
            'pcB:2.background_color' => $sectionBackgroundColor
        ];

    }

    public function testParallaxContainer()
    {
        // wrap mode:

        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $elements = $this->getDefaultElements($asset);

        $this->assertEquals(
            $this->filter($this->getCompare($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('parallaxContainer', $elements))
        );
    }

    public function testParallaxContainerWithAdditionalClasses()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', true);

        $elements = $this->getDefaultElements($asset);

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements['add_classes'] = $combo;

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset->getFullPath())),
            $this->filter($this->generateRenderedArea('parallaxContainer', $elements))
        );
    }

    private function getCompare($imagePath)
    {
        return '<div class="toolbox-element toolbox-parallax-container template-no-template ">
                    <div class="parallax-background " data-background-image="' . $imagePath . '" data-background-color="default">
                        <div class="behind-elements">
                            <div class="element position-top-left size-third-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="top-left"        data-element-size="third-window-width"></div>
                            <div class="element position-center-right size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="center-right"        data-element-size="half-window-width"></div>
                        </div>
                        <div class="parallax-content">
                            <div class="parallax-section template-no-template " data-background-image="' . $imagePath . '" data-loop-index="1" data-section-index="1" data-template="no-template">
                                <div class="toolbox-container">
                                    <div class="container-fluid">
                                        <div class="container-inner"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="parallax-section template-no-template " data-background-image="' . $imagePath . '" data-loop-index="2" data-section-index="2" data-template="no-template">
                                <div class="toolbox-container">
                                    <div class="container-fluid">
                                        <div class="container-inner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="front-elements">
                            <div class="element position-top-left size-quarter-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="top-left"        data-element-size="quarter-window-width"></div>
                            <div class="element position-top-left size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="top-left"        data-element-size="half-window-width"></div>
                        </div>
                    </div>
                </div>';

    }

    private function getCompareWithAdditionalClass($imagePath)
    {
        return '<div class="toolbox-element toolbox-parallax-container template-no-template additional-class">
                    <div class="parallax-background " data-background-image="' . $imagePath . '" data-background-color="default">
                        <div class="behind-elements">
                            <div class="element position-top-left size-third-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="top-left"        data-element-size="third-window-width"></div>
                            <div class="element position-center-right size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="center-right"        data-element-size="half-window-width"></div>
                        </div>
                        <div class="parallax-content">
                            <div class="parallax-section template-no-template " data-background-image="' . $imagePath . '" data-loop-index="1" data-section-index="1" data-template="no-template">
                                <div class="toolbox-container">
                                    <div class="container-fluid">
                                        <div class="container-inner"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="parallax-section template-no-template " data-background-image="' . $imagePath . '" data-loop-index="2" data-section-index="2" data-template="no-template">
                                <div class="toolbox-container">
                                    <div class="container-fluid">
                                        <div class="container-inner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="front-elements">
                            <div class="element position-top-left size-quarter-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="top-left"        data-element-size="quarter-window-width"></div>
                            <div class="element position-top-left size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="" data-height=""        data-element-position="top-left"        data-element-size="half-window-width"></div>
                        </div>
                    </div>
                </div>';

    }
}
