<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Select;

class SpacerTest extends AbstractAreaTest
{
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
            $this->filter($this->generateRenderedArea('spacer', $elements))
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
            $this->filter($this->generateRenderedArea('spacer', $elements))
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
