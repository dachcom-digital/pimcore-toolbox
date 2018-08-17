<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Block;
use Pimcore\Model\Document\Tag\Link;
use Pimcore\Model\Document\Tag\Select;

class LinkListTest extends AbstractAreaTest
{
    const TYPE = 'linkList';

    public function testLinkListBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(0, $configElements);
    }

    public function testLinkList()
    {
        $this->setupRequest();

        $link1 = new Link();
        $link1->setDataFromEditmode(['path' => 'https://www.dachcom.com']);

        $link2 = new Link();
        $link2->setDataFromEditmode(['path' => 'https://www.dachcom-digital.com']);

        $block = new Block();
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

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $link1 = new Link();
        $link1->setDataFromEditmode(['path' => 'https://www.dachcom.com']);

        $link2 = new Link();
        $link2->setDataFromEditmode(['path' => 'https://www.dachcom-digital.com']);

        $block = new Block();
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
                            <a href="https://www.dachcom.com" class="list-link"></a>
                        </li>
                        <li>
                            <a href="https://www.dachcom-digital.com" class="list-link"></a>
                        </li>
                    </ul>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-linklist additional-class">
                    <ul>
                        <li>
                            <a href="https://www.dachcom.com" class="list-link"></a>
                        </li>
                        <li>
                            <a href="https://www.dachcom-digital.com" class="list-link"></a>
                        </li>
                    </ul>
                </div>';
    }
}
