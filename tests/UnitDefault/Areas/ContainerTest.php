<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Select;

class ContainerTest extends AbstractAreaTest
{
    public const TYPE = 'container';

    public function testContainerBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(1, $configElements);
        $this->assertEquals('checkbox', $configElements[0]['type']);
        $this->assertEquals('full_width_container', $configElements[0]['name']);
    }

    public function testImage()
    {
        $this->setupRequest();

        $elements = [
        ];

        $this->assertEquals(
            $this->filter($this->getCompareDefault()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testContainerWithFullWidth()
    {
        $this->setupRequest();

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'full_width_container' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithFullWidth()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );

    }

    public function testContainerWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'add_classes' => $combo,
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompareDefault()
    {
        return '<div class="toolbox-element toolbox-container ">
                    <div class="container">
                        <div class="container-inner"></div>
                    </div>
                </div>';
    }

    private function getCompareWithFullWidth()
    {
        return '<div class="toolbox-element toolbox-container ">
                    <div class="container-fluid">
                        <div class="container-inner"></div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-container additional-class">
                    <div class="container">
                        <div class="container-inner"></div>
                    </div>
                </div>';
    }
}
