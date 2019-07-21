<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

class ParallaxContainerSectionTest extends AbstractAreaTest
{
    const TYPE = 'parallaxContainerSection';

    public function testParallaxContainerSectionBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(4, $configElements);
        $this->assertEquals('select', $configElements[0]['additional_config']['type']);
        $this->assertEquals('template', $configElements[0]['additional_config']['name']);

        $this->assertEquals('select', $configElements[1]['additional_config']['type']);
        $this->assertEquals('container_type', $configElements[1]['additional_config']['name']);

        $this->assertEquals('relation', $configElements[2]['additional_config']['type']);
        $this->assertEquals('background_image', $configElements[2]['additional_config']['name']);

        $this->assertEquals('select', $configElements[3]['additional_config']['type']);
        $this->assertEquals('background_color', $configElements[3]['additional_config']['name']);
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
