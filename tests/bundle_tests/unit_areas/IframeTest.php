<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Input;
use Pimcore\Model\Document\Tag\Numeric;
use Pimcore\Model\Document\Tag\Select;

class IframeTest extends AbstractAreaTest
{
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
            $this->filter($this->generateRenderedArea('iFrame', $elements))
        );
    }

    public function testIframeWithHeight()
    {
        $this->setupRequest();

        $url = new Input();
        $url->setDataFromResource('https://www.dachcom.com');

        $height = new Numeric();
        $height->setDataFromResource(200);

        $elements = [
            'url'     => $url,
            'iheight' => $height
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithHeight()),
            $this->filter($this->generateRenderedArea('iFrame', $elements))
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
            $this->filter($this->generateRenderedArea('iFrame', $elements))
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
