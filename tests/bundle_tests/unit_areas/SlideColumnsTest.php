<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Select;

class SlideColumnsTest extends AbstractAreaTest
{
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

        $slidesPerView = new Select();
        $slidesPerView->setDataFromResource('4');

        $elements = [
            'slides_per_view' => $slidesPerView
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea('slideColumns', $elements))
        );
    }

    public function testSlideColumnsWithEqualHeight()
    {
        $this->setupRequest();

        $slidesPerView = new Select();
        $slidesPerView->setDataFromResource('4');

        $equalHeight = new Checkbox();
        $equalHeight->setDataFromEditmode(1);

        $elements = [
            'slides_per_view' => $slidesPerView,
            'equal_height'    => $equalHeight
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithEqualHeight()),
            $this->filter($this->generateRenderedArea('slideColumns', $elements))
        );
    }

    public function testSlideColumnsWithBreakPoints()
    {
        $this->setupRequest();

        $slidesPerView = new Select();
        $slidesPerView->setDataFromResource('4');

        $equalHeight = new Checkbox();
        $equalHeight->setDataFromEditmode(1);

        $configManager = $this->getToolboxConfig();

        $slideColumns = $configManager->getConfig('slideColumns');
        $theme = $configManager->getConfig('theme');

        $slideColumns['config_parameter'] = [
            'column_classes' => [
                4 => 'col-12 col-sm-12 col-lg-2',
            ],
            'breakpoints'    => [
                4 => 'col-12 col-sm-12 col-lg-2'
            ]
        ];

        $configManager->setConfig(['areas' => ['slideColumns' => $slideColumns], 'theme' => $theme]);

        $elements = [
            'slides_per_view' => $slidesPerView,
            'equal_height'    => $equalHeight
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithBreakPoints()),
            $this->filter($this->generateRenderedArea('slideColumns', $elements))
        );
    }

    public function testSlideColumnsWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $slidesPerView = new Select();
        $slidesPerView->setDataFromResource('4');

        $elements = [
            'slides_per_view' => $slidesPerView,
            'add_classes'     => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea('slideColumns', $elements))
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
