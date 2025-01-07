<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable;

class LinkListTest extends AbstractAreaTest
{
    public const TYPE = 'linkList';

    public function testLinkListBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(0, $configElements);
    }

    public function testLinkList()
    {
        $this->setupRequest();

        $link1 = new Editable\Link();
        $link1->setDataFromEditmode(['path' => 'https://www.dachcom.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $link2 = new Editable\Link();
        $link2->setDataFromEditmode(['path' => 'https://www.dachcom-digital.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $block = new Editable\Block();
        $block->setName('test-block-name');
        $block->setDataFromEditmode([1, 2]);

        $elements = [
            'linklistblock'            => $block,
            'linklistblock:1.linklist' => $link1,
            'linklistblock:2.linklist' => $link2
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testLinkListWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Editable\Select();
        $combo->setDataFromResource('additional-class');

        $link1 = new Editable\Link();
        $link1->setDataFromEditmode(['path' => 'https://www.dachcom.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $link2 = new Editable\Link();
        $link2->setDataFromEditmode(['path' => 'https://www.dachcom-digital.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $block = new Editable\Block();
        $block->setName('test-block-name');
        $block->setDataFromEditmode([1, 2]);

        $elements = [
            'linklistblock'            => $block,
            'linklistblock:1.linklist' => $link1,
            'linklistblock:2.linklist' => $link2,
            'add_classes'              => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-linklist ">
                    <ul>
                        <li>
                            <a href="https://www.dachcom.com" path="https://www.dachcom.com" linktype="direct" text="dummy" class="list-link">dummy</a>
                        </li>
                        <li>
                            <a href="https://www.dachcom-digital.com" class="list-link">dummy</a>
                        </li>
                    </ul>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-linklist additional-class">
                    <ul>
                        <li>
                            <a href="https://www.dachcom.com" path="https://www.dachcom.com" linktype="direct" text="dummy" class="list-link">dummy</a>
                        </li>
                        <li>
                            <a href="https://www.dachcom-digital.com" class="list-link">dummy</a>
                        </li>
                    </ul>
                </div>';
    }
}
