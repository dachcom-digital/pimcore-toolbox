<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable;

class SlideColumnsTest extends AbstractAreaTest
{
    public const TYPE = 'slideColumns';

    public function testSlideColumnsBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(2, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('slides_per_view', $configElements[0]['name']);

        $this->assertEquals('checkbox', $configElements[1]['type']);
        $this->assertEquals('equal_height', $configElements[1]['name']);
    }

    public function testSlideColumnsConfigParameter()
    {
        $configParam = $this->getToolboxConfig()->getAreaParameterConfig('slideColumns');
        $this->assertEquals(
            [
                'column_classes' => [
                    '2' => 'col-12 col-sm-6'
                ],
                'breakpoints'    => []
            ],
            $configParam
        );
    }

    public function testSlideColumns()
    {
        $this->setupRequest();

        $slidesPerView = new Editable\Select();
        $slidesPerView->setDataFromResource('4');

        $elements = [
            'slides_per_view' => $slidesPerView
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testSlideColumnsWithEqualHeight()
    {
        $this->setupRequest();

        $slidesPerView = new Editable\Select();
        $slidesPerView->setDataFromResource('4');

        $equalHeight = new Editable\Checkbox();
        $equalHeight->setDataFromEditmode(1);

        $elements = [
            'slides_per_view' => $slidesPerView,
            'equal_height'    => $equalHeight
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithEqualHeight()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testSlideColumnsWithBreakPoints()
    {
        $this->setupRequest();

        $breakpoints = [
            4 => [
                1320 =>
                    [
                        'slidesToShow' => 4,
                    ],
                992  =>
                    [
                        'slidesToShow' => 3,
                    ],
                768  =>
                    [
                        'slidesToShow' => 2,
                        'arrows'       => true,
                        'dots'         => false,
                    ],
                0    =>
                    [
                        'slidesToShow' => 1,
                        'arrows'       => true,
                        'dots'         => false,
                    ],
            ]
        ];

        $slidesPerView = new Editable\Select();
        $slidesPerView->setDataFromResource('4');

        $equalHeight = new Editable\Checkbox();
        $equalHeight->setDataFromEditmode(1);

        $configManager = $this->getToolboxConfig();

        $slideColumns = $configManager->getAreaConfig(self::TYPE);
        $theme = $configManager->getConfig('theme');

        $slideColumns['config_parameter'] = [
            'column_classes' => [
                4 => 'col-12 col-sm-12 col-lg-2',
            ],
            'breakpoints'    => $breakpoints
        ];

        $configManager->setConfig([
            'areas'                    => [self::TYPE => $slideColumns],
            'theme'                    => $theme,
            'area_block_configuration' => [
                'toolbar'         => [],
                'controlsAlign'   => 'top',
                'controlsTrigger' => 'hover',
            ],
            'areas_appearance'         => [],
        ]);

        $elements = [
            'slides_per_view' => $slidesPerView,
            'equal_height'    => $equalHeight
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithBreakPoints($breakpoints[4])),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testSlideColumnsWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Editable\Select();
        $combo->setDataFromResource('additional-class');

        $slidesPerView = new Editable\Select();
        $slidesPerView->setDataFromResource('4');

        $elements = [
            'slides_per_view' => $slidesPerView,
            'add_classes'     => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-slide-columns  ">
                    <div class="row">
                        <div class="slide-columns slide-elements-4 slideColumns-0" data-slides="4" data-breakpoints="[]">
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-1"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-2"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-3"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-4"></div>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithEqualHeight()
    {
        return '<div class="toolbox-element toolbox-slide-columns  equal-height">
                    <div class="row">
                        <div class="slide-columns slide-elements-4 slideColumns-0" data-slides="4" data-breakpoints="[]">
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-1"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-2"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-3"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-4"></div>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithBreakPoints(array $breakPoints = [])
    {
        return '<div class="toolbox-element toolbox-slide-columns  equal-height">
                    <div class="row">
                        <div class="slide-columns slide-elements-4 slideColumns-0" data-slides="4" data-breakpoints="' . htmlspecialchars(json_encode($breakPoints)) . '">
                            <div class="column col-12 col-sm-12 col-lg-2">
                                <div class="slide-column slide-1"></div>
                            </div>
                            <div class="column col-12 col-sm-12 col-lg-2">
                                <div class="slide-column slide-2"></div>
                            </div>
                            <div class="column col-12 col-sm-12 col-lg-2">
                                <div class="slide-column slide-3"></div>
                            </div>
                            <div class="column col-12 col-sm-12 col-lg-2">
                                <div class="slide-column slide-4"></div>
                            </div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-slide-columns additional-class ">
                    <div class="row">
                        <div class="slide-columns slide-elements-4 slideColumns-0" data-slides="4" data-breakpoints="[]">
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-1"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-2"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-3"></div>
                            </div>
                            <div class="column col-12 col-sm-3">
                                <div class="slide-column slide-4"></div>
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
