<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Select;

class SeparatorTest extends AbstractAreaTest
{
    const TYPE = 'separator';

    public function testSeparatorBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(1, $configElements);
        $this->assertEquals('select', $configElements[0]['additional_config']['type']);
        $this->assertEquals('space', $configElements[0]['additional_config']['name']);
    }

    public function testSeparator()
    {
        $this->setupRequest();

        $space = new Select();
        $space->setDataFromEditmode('large');

        $elements = [
            'space' => $space
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testSeperatorWithAdditionalClass()
    {
        $this->setupRequest();

        $space = new Select();
        $space->setDataFromEditmode('large');

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'space'       => $space,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-separator "><hr class="large"></div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-separator additional-class"><hr class="large"></div>';
    }
}
