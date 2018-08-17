<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Input;
use Pimcore\Model\Document\Tag\Select;

class HeadlineTest extends AbstractAreaTest
{
    const TYPE = 'headline';

    public function testHeadlineBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(2, $configElements);
        $this->assertEquals('select', $configElements[0]['additional_config']['type']);
        $this->assertEquals('headline_type', $configElements[0]['additional_config']['name']);

        $this->assertEquals('input', $configElements[1]['additional_config']['type']);
        $this->assertEquals('anchor_name', $configElements[1]['additional_config']['name']);
    }

    public function testHeadline()
    {
        $this->setupRequest();

        $headlineType = new Select();
        $headlineType->setDataFromResource('h6');

        $headlineText = new Input();
        $headlineText->setDataFromResource('this is a headline');

        $elements = [
            'headline_type' => $headlineType,
            'headline_text' => $headlineText
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testHeadlineWithAnchorName()
    {
        $this->setupRequest();

        $headlineType = new Select();
        $headlineType->setDataFromResource('h6');

        $headlineText = new Input();
        $headlineText->setDataFromResource('this is a headline');

        $anchorName = new Input();
        $anchorName->setDataFromResource('anchorName');

        $elements = [
            'headline_type' => $headlineType,
            'headline_text' => $headlineText,
            'anchor_name'   => $anchorName
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAnchorName()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testHeadlineWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $headlineType = new Select();
        $headlineType->setDataFromResource('h6');

        $headlineText = new Input();
        $headlineText->setDataFromResource('this is a headline');

        $elements = [
            'headline_type' => $headlineType,
            'headline_text' => $headlineText,
            'add_classes'   => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-headline "><h6>this is a headline</h6></div>';
    }

    private function getCompareWithAnchorName()
    {
        return '<div class="toolbox-element toolbox-headline "><a class="toolbox-anchor" id="anchorname"></a><h6>this is a headline</h6></div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-headline additional-class"><h6>this is a headline</h6></div>';
    }
}
