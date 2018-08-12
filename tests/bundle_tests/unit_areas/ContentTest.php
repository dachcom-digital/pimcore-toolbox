<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Select;

class ContentTest extends AbstractAreaTest
{
    public function testContent()
    {
        $this->setupRequest();

        $elements = [];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea('content', $elements))
        );
    }

     public function testContentWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea('content', $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-content wysiwyg content-container "></div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-content wysiwyg content-container additional-class"></div>';
    }
}
