<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Select;

class SeparatorTest extends AbstractAreaTest
{
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
            $this->filter($this->generateRenderedArea('separator', $elements))
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
            $this->filter($this->generateRenderedArea('separator', $elements))
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
