<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Select;
use ToolboxBundle\Model\Document\Editable\ColumnAdjuster;

class ColumnsTest extends AbstractAreaTest
{
    public const TYPE = 'columns';

    public function testColumnsBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(3, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('type', $configElements[0]['name']);

        $this->assertEquals('columnadjuster', $configElements[1]['type']);
        $this->assertEquals('columnadjuster', $configElements[1]['name']);

        $this->assertEquals('checkbox', $configElements[2]['type']);
        $this->assertEquals('equal_height', $configElements[2]['name']);
    }

    public function testDefaultColumns()
    {
        $this->setupRequest();

        $type = new Select();
        $type->setDataFromResource('column_4_8');

        $elements = [
            'type' => $type
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testColumnsWithEqualHeight()
    {
        $this->setupRequest();

        $type = new Select();
        $type->setDataFromResource('column_4_8');

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'type'         => $type,
            'equal_height' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithEqualHeights()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testColumnsWithAdditionalClass()
    {
        $this->setupRequest();

        $type = new Select();
        $type->setDataFromResource('column_4_8');

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'type'        => $type,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testColumnsWithColumnsAdjuster()
    {
        $this->setupRequest();

        $type = new Select();
        $type->setDataFromResource('column_4_8');

        $combo = new ColumnAdjuster();
        $combo->setDataFromResource(serialize([
            'breakpoints' => [
                'xs' => '4_4',
                'sm' => '4_8',
                'lg' => '1_11'
            ]
        ]));

        $elements = [
            'type'           => $type,
            'columnadjuster' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithColumnsAdjuster()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-columns type-column_4_8  ">
                    <div class="row">
                        <div class="col-12 col-sm-4 ">
                            <div class="toolbox-column"></div>
                        </div>
                        <div class="col-12 col-sm-8 ">
                            <div class="toolbox-column"></div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithEqualHeights()
    {
        return '<div class="toolbox-element toolbox-columns type-column_4_8  equal-height">
                    <div class="row">
                        <div class="col-12 col-sm-4 ">
                            <div class="toolbox-column equal-height-item"></div>
                        </div>
                        <div class="col-12 col-sm-8 ">
                            <div class="toolbox-column equal-height-item"></div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-columns type-column_4_8 additional-class ">
                    <div class="row">
                        <div class="col-12 col-sm-4 ">
                            <div class="toolbox-column"></div>
                        </div>
                        <div class="col-12 col-sm-8 ">
                            <div class="toolbox-column"></div>
                        </div>
                    </div>
                </div>';
    }

    private function getCompareWithColumnsAdjuster()
    {
        return '<div class="toolbox-element toolbox-columns type-column_4_8-grid-adjuster  ">
                    <div class="row">
                        <div class="col-4 col-sm-4 col-lg-1 ">
                            <div class="toolbox-column"></div>
                        </div>
                        <div class="col-4 col-sm-8 col-lg-11 ">
                            <div class="toolbox-column"></div>
                        </div>
                    </div>
                </div>';
    }
}
