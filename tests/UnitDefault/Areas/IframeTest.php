<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Input;
use Pimcore\Model\Document\Editable\Numeric;
use Pimcore\Model\Document\Editable\Select;

class IframeTest extends AbstractAreaTest
{
    public const TYPE = 'iFrame';

    public function testIframeBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(2, $configElements);
        $this->assertEquals('input', $configElements[0]['type']);
        $this->assertEquals('url', $configElements[0]['name']);

        $this->assertEquals('numeric', $configElements[1]['type']);
        $this->assertEquals('iheight', $configElements[1]['name']);
    }

    public function testIframe()
    {
        $this->setupRequest();

        $url = new Input();
        $url->setDataFromResource('https://www.dachcom.com');

        $elements = [
            'url' => $url
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testIframeWithHeight()
    {
        $this->setupRequest();

        $url = new Input();
        $url->setDataFromResource('https://www.dachcom.com');

        $height = new Numeric();
        $height->setDataFromResource('200');

        $elements = [
            'url'     => $url,
            'iheight' => $height
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithHeight()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testIframeWithAdditionalClass()
    {
        $this->setupRequest();

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-iframe ">
                    <iframe src="https://www.dachcom.com" frameborder="0" width="100%" ></iframe>
                </div>';
    }

    private function getCompareWithHeight()
    {
        return '<div class="toolbox-element toolbox-iframe ">
                    <iframe src="https://www.dachcom.com" frameborder="0" width="100%" height="200px"></iframe>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-iframe additional-class">
                    <iframe src="" frameborder="0" width="100%" ></iframe>
                </div>';
    }
}
