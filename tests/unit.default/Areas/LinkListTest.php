<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Dachcom\Codeception\Util\VersionHelper;

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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $blockClass = 'Pimcore\Model\Document\Editable\Block';
            $linkClass = 'Pimcore\Model\Document\Editable\Link';
        } else {
            $blockClass = 'Pimcore\Model\Document\Editable\Block';
            $linkClass = 'Pimcore\Model\Document\Editable\Link';
        }

        $link1 = new $linkClass();
        $link1->setDataFromEditmode(['path' => 'https://www.dachcom.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $link2 = new $linkClass();
        $link2->setDataFromEditmode(['path' => 'https://www.dachcom-digital.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $block = new $blockClass();
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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $blockClass = 'Pimcore\Model\Document\Editable\Block';
            $linkClass = 'Pimcore\Model\Document\Editable\Link';
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
        } else {
            $blockClass = 'Pimcore\Model\Document\Editable\Block';
            $linkClass = 'Pimcore\Model\Document\Editable\Link';
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
        }

        $combo = new $selectClass();
        $combo->setDataFromResource('additional-class');

        $link1 = new $linkClass();
        $link1->setDataFromEditmode(['path' => 'https://www.dachcom.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $link2 = new $linkClass();
        $link2->setDataFromEditmode(['path' => 'https://www.dachcom-digital.com', 'linktype' => 'direct', 'text' => 'dummy']);

        $block = new $blockClass();
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
                            <a href="https://www.dachcom.com" class="list-link">dummy</a>
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
                            <a href="https://www.dachcom.com" class="list-link">dummy</a>
                        </li>
                        <li>
                            <a href="https://www.dachcom-digital.com" class="list-link">dummy</a>
                        </li>
                    </ul>
                </div>';
    }
}
