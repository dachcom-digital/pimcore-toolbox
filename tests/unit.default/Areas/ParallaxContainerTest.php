<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Tests\Util\TestHelper;
use Pimcore\Model\Document\Editable;
use ToolboxBundle\Model\Document\Editable\ParallaxImage;

class ParallaxContainerTest extends AbstractAreaTest
{
    public const TYPE = 'parallaxContainer';

    public function testParallaxContainerBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(5, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('template', $configElements[0]['name']);

        $this->assertEquals('relation', $configElements[1]['type']);
        $this->assertEquals('background_image', $configElements[1]['name']);

        $this->assertEquals('select', $configElements[2]['type']);
        $this->assertEquals('background_color', $configElements[2]['name']);

        $this->assertEquals('parallaximage', $configElements[3]['type']);
        $this->assertEquals('image_front', $configElements[3]['name']);

        $this->assertEquals('parallaximage', $configElements[4]['type']);
        $this->assertEquals('image_behind', $configElements[4]['name']);
    }

    public function testParallaxContainerConfigParameter()
    {
        $configParam = $this->getToolboxConfig()->getAreaParameterConfig(self::TYPE);
        $this->assertEquals(
            [
                'window_size'           => 'large',
                'background_mode'       => 'wrap',
                'background_image_mode' => 'data',
                'background_color_mode' => 'data'
            ],
            $configParam
        );
    }

    public function testParallaxContainer()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $elements = $this->getDefaultElements($asset);

        $this->assertEquals(
            $this->filter($this->getCompare($asset->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testParallaxContainerWithAdditionalClass()
    {
        $this->setupRequest();

        $asset = TestHelper::createImageAsset('', null);

        $elements = $this->getDefaultElements($asset);


        $combo = new Editable\Select();
        $combo->setDataFromResource('additional-class');

        $elements['add_classes'] = $combo;

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getDefaultElements($asset)
    {
        $backgroundImage = new Editable\Relation();
        $backgroundImage->setDataFromEditmode([
            'id'      => $asset->getId(),
            'type'    => 'asset',
            'subtype' => null,
        ]);

        $template = new Editable\Select();
        $template->setDataFromEditmode('no-template');

        $backgroundColor = new Editable\Select();
        $backgroundColor->setDataFromEditmode('default');

        $imageFront = new ParallaxImage();
        $imageFront->setDataFromEditmode([
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'subtype'          => null,
                'parallaxPosition' => 'top-left',
                'parallaxSize'     => 'quarter-window-width'
            ],
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'subtype'          => null,
                'parallaxPosition' => 'top-left',
                'parallaxSize'     => 'half-window-width'
            ]
        ]);

        $imageBehind = new ParallaxImage();
        $imageBehind->setDataFromEditmode([
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'subtype'          => null,
                'parallaxPosition' => 'top-left',
                'parallaxSize'     => 'third-window-width'
            ],
            [
                'id'               => $asset->getId(),
                'type'             => 'asset',
                'subtype'          => null,
                'parallaxPosition' => 'center-right',
                'parallaxSize'     => 'half-window-width'
            ]
        ]);

        $block = new Editable\Block();
        $block->setName('test-parallax-container-section');
        $block->setDataFromEditmode([1, 2]);

        $sectionTemplate = new Editable\Select();
        $sectionTemplate->setDataFromEditmode('no-template');

        $sectionContainerType = new Editable\Select();
        $sectionContainerType->setDataFromEditmode('container-fluid');

        $sectionBackgroundImage = new Editable\Relation();
        $sectionBackgroundImage->setDataFromEditmode([
            'id'      => $asset->getId(),
            'type'    => 'asset',
            'subtype' => null,
        ]);

        $sectionBackgroundColor = new Editable\Select();
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

    private function getCompare($imagePath)
    {
        return '<div class="toolbox-element toolbox-parallax-container template-no-template ">
                    <div class="parallax-background " data-background-image="' . $imagePath . '" data-background-color="default">
                        <div class="behind-elements">
                            <div class="element position-top-left size-third-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="top-left"        data-element-size="third-window-width"></div>
                            <div class="element position-center-right size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="center-right"        data-element-size="half-window-width"></div>
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
                            <div class="element position-top-left size-quarter-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="top-left"        data-element-size="quarter-window-width"></div>
                            <div class="element position-top-left size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="top-left"        data-element-size="half-window-width"></div>
                        </div>
                    </div>
                </div>';

    }

    private function getCompareWithAdditionalClass($imagePath)
    {
        return '<div class="toolbox-element toolbox-parallax-container template-no-template additional-class">
                    <div class="parallax-background " data-background-image="' . $imagePath . '" data-background-color="default">
                        <div class="behind-elements">
                            <div class="element position-top-left size-third-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="top-left"        data-element-size="third-window-width"></div>
                            <div class="element position-center-right size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="center-right"        data-element-size="half-window-width"></div>
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
                            <div class="element position-top-left size-quarter-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="top-left"        data-element-size="quarter-window-width"></div>
                            <div class="element position-top-left size-half-window-width"        data-background-image="' . $imagePath . '"        data-width="700" data-height="467"        data-element-position="top-left"        data-element-size="half-window-width"></div>
                        </div>
                    </div>
                </div>';

    }
}
