<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Input;

class AnchorTest extends AbstractAreaTest
{
    public function testAccordion()
    {
        $this->setupRequest();

        $name = new Input();
        $name->setDataFromResource('AnchorId');

        $title = new Input();
        $title->setDataFromResource('Anchor Title');

        $elements = [
            'anchor_name'  => $name,
            'anchor_title' => $title
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea('anchor', $elements))
        );
    }

    private function getCompare()
    {
        return '<a class="toolbox-element toolbox-anchor" id="AnchorId" data-anchortitle="Anchor Title"></a>';
    }
}
