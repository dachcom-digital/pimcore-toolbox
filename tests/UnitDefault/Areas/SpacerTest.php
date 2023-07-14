<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Select;

class SpacerTest extends AbstractAreaTest
{
    public const TYPE = 'spacer';

    public function testSpacerBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(1, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('spacer_class', $configElements[0]['name']);
    }

    public function testSpacer()
    {
        $this->setupRequest();

        $spacerClass = new Select();
        $spacerClass->setDataFromEditmode('spacer-50');

        $elements = [
            'spacer_class' => $spacerClass
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

     public function testSpacerWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $spacerClass = new Select();
        $spacerClass->setDataFromEditmode('spacer-50');

        $elements = [
            'add_classes' => $combo,
            'spacer_class' => $spacerClass
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-spacer " ><span class="spacer-50"></span></div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-spacer additional-class" ><span class="spacer-50"></span></div>';
    }
}
