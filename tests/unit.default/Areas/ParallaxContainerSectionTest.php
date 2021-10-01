<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

class ParallaxContainerSectionTest extends AbstractAreaTest
{
    const TYPE = 'parallaxContainerSection';

    public function testParallaxContainerSectionBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(4, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('template', $configElements[0]['name']);

        $this->assertEquals('select', $configElements[1]['type']);
        $this->assertEquals('container_type', $configElements[1]['name']);

        $this->assertEquals('relation', $configElements[2]['type']);
        $this->assertEquals('background_image', $configElements[2]['name']);

        $this->assertEquals('select', $configElements[3]['type']);
        $this->assertEquals('background_color', $configElements[3]['name']);
    }

    public function testParallaxContainerConfigParameter()
    {
        $configParam = $this->getToolboxConfig()->getAreaParameterConfig(self::TYPE);
        $this->assertEquals(
            [
                'background_image_mode' => 'data',
                'background_color_mode' => 'data'
            ],
            $configParam
        );
    }
}
