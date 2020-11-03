<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use DachcomBundle\Test\Util\VersionHelper;

class SlideColumnsTest extends AbstractAreaTest
{
    const TYPE = 'slideColumns';

    public function testSlideColumnsBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(2, $configElements);
        $this->assertEquals('select', $configElements[0]['additional_config']['type']);
        $this->assertEquals('slides_per_view', $configElements[0]['additional_config']['name']);

        $this->assertEquals('checkbox', $configElements[1]['additional_config']['type']);
        $this->assertEquals('equal_height', $configElements[1]['additional_config']['name']);
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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
        } else {
            $selectClass = 'Pimcore\Model\Document\Tag\Select';
        }

        $slidesPerView = new $selectClass();
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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
            $checkboxClass = 'Pimcore\Model\Document\Editable\Checkbox';
        } else {
            $selectClass = 'Pimcore\Model\Document\Tag\Select';
            $checkboxClass = 'Pimcore\Model\Document\Tag\Checkbox';
        }

        $slidesPerView = new $selectClass();
        $slidesPerView->setDataFromResource('4');

        $equalHeight = new $checkboxClass();
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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
            $checkboxClass = 'Pimcore\Model\Document\Editable\Checkbox';
        } else {
            $selectClass = 'Pimcore\Model\Document\Tag\Select';
            $checkboxClass = 'Pimcore\Model\Document\Tag\Checkbox';
        }

        $slidesPerView = new $selectClass();
        $slidesPerView->setDataFromResource('4');

        $equalHeight = new $checkboxClass();
        $equalHeight->setDataFromEditmode(1);

        $configManager = $this->getToolboxConfig();

        $slideColumns = $configManager->getAreaConfig(self::TYPE);
        $theme = $configManager->getConfig('theme');

        $slideColumns['config_parameter'] = [
            'column_classes' => [
                4 => 'col-12 col-sm-12 col-lg-2',
            ],
            'breakpoints'    => [
                4 => 'col-12 col-sm-12 col-lg-2'
            ]
        ];

        $configManager->setConfig([
            'areas'                    => [self::TYPE => $slideColumns],
            'theme'                    => $theme,
            'area_block_configuration' => [],
            'areas_appearance'         => [],
        ]);

        $elements = [
            'slides_per_view' => $slidesPerView,
            'equal_height'    => $equalHeight
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithBreakPoints()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testSlideColumnsWithAdditionalClass()
    {
        $this->setupRequest();

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
        } else {
            $selectClass = 'Pimcore\Model\Document\Tag\Select';
        }

        $combo = new $selectClass();
        $combo->setDataFromResource('additional-class');

        $slidesPerView = new $selectClass();
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
                        <div class="slide-columns slide-elements-4 slideColumns-1" data-slides="4" data-breakpoints="[]">
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
                        <div class="slide-columns slide-elements-4 slideColumns-1" data-slides="4" data-breakpoints="[]">
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

    private function getCompareWithBreakPoints()
    {
        return '<div class="toolbox-element toolbox-slide-columns  equal-height">
                    <div class="row">
                        <div class="slide-columns slide-elements-4 slideColumns-1" data-slides="4" data-breakpoints="&quot;col-12 col-sm-12 col-lg-2&quot;">
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
                        <div class="slide-columns slide-elements-4 slideColumns-1" data-slides="4" data-breakpoints="[]">
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
