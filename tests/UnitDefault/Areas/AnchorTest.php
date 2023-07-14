<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Input;

class AnchorTest extends AbstractAreaTest
{
    const TYPE = 'anchor';

    public function testAnchorBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(2, $configElements);
        $this->assertEquals('input', $configElements[0]['type']);
        $this->assertEquals('anchor_name', $configElements[0]['name']);

        $this->assertEquals('input', $configElements[1]['type']);
        $this->assertEquals('anchor_title', $configElements[1]['name']);
    }

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
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<a class="toolbox-element toolbox-anchor" id="AnchorId" data-anchortitle="Anchor Title"></a>';
    }
}
