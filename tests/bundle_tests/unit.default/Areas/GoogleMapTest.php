<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Numeric;
use Pimcore\Model\Document\Tag\Select;
use ToolboxBundle\Model\Document\Tag\GoogleMap;

class GoogleMapTest extends AbstractAreaTest
{
    const TYPE = 'googleMap';

    public function testGoogleMapBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(3, $configElements);
        $this->assertEquals('numeric', $configElements[0]['additional_config']['type']);
        $this->assertEquals('map_zoom', $configElements[0]['additional_config']['name']);

        $this->assertEquals('select', $configElements[1]['additional_config']['type']);
        $this->assertEquals('map_type', $configElements[1]['additional_config']['name']);

        $this->assertEquals('checkbox', $configElements[2]['additional_config']['type']);
        $this->assertEquals('iw_on_init', $configElements[2]['additional_config']['name']);

    }

    public function testGoogleMapConfigParameter()
    {
        $configParam = $this->getToolboxConfig()->getAreaParameterConfig('googleMap');
        $this->assertEquals(
            [
                'map_options'   => [
                    'streetViewControl' => true,
                    'mapTypeControl'    => false,
                    'panControl'        => false,
                    'scrollwheel'       => false
                ],
                'map_style_url' => false,
                'marker_icon'   => false,
                'map_api_key'   => '',
            ],
            $configParam
        );
    }

    public function testGoogleMap()
    {
        $this->setupRequest();

        $googleMapElement = new GoogleMap();
        $googleMapElement->setId('test');
        $googleMapElement->disableGoogleLookup();

        $googleMapElement->setDataFromEditmode([
            [
                'street'  => 'Rorschacherstrasse 15',
                'zip'     => '9424',
                'city'    => 'Rheineck',
                'country' => 'Schweiz'
            ]
        ]);

        $mapZoom = new Numeric();
        $mapZoom->setDataFromResource(5);

        $mapType = new Select();
        $mapZoom->setDataFromResource('roadmap');

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $elements = [
            'googlemap'  => $googleMapElement,
            'map_zoom'   => $mapZoom,
            'map_type'   => $mapType,
            'iw_on_init' => $iwOnInit
        ];

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testGoogleMapWidthAdditionalClass()
    {
        $this->setupRequest();

        $googleMapElement = new GoogleMap();
        $googleMapElement->setId('test');
        $googleMapElement->disableGoogleLookup();

        $googleMapElement->setDataFromEditmode([
            [
                'street'  => 'Rorschacherstrasse 15',
                'zip'     => '9424',
                'city'    => 'Rheineck',
                'country' => 'Schweiz'
            ]
        ]);

        $mapZoom = new Numeric();
        $mapZoom->setDataFromResource(5);

        $mapType = new Select();
        $mapZoom->setDataFromResource('roadmap');

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'googlemap'   => $googleMapElement,
            'map_zoom'    => $mapZoom,
            'map_type'    => $mapType,
            'iw_on_init'  => $iwOnInit,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-google-map ">
                    <div class="toolbox-google-map-container">
                        <div class="embed-responsive-item toolbox-googlemap" id="test" data-locations=\'[{"street":"Rorschacherstrasse 15","zip":"9424","city":"Rheineck","country":"Schweiz"}]\' data-show-info-window-on-load=\'1\' data-mapoption-zoom=\'roadmap\' data-mapoption-map-type-id=\'\' data-mapoption-street-view-control=\'true\' data-mapoption-map-type-control=\'false\' data-mapoption-pan-control=\'false\' data-mapoption-scrollwheel=\'false\'></div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '<div class="toolbox-element toolbox-google-map additional-class">
                    <div class="toolbox-google-map-container">
                        <div class="embed-responsive-item toolbox-googlemap" id="test" data-locations=\'[{"street":"Rorschacherstrasse 15","zip":"9424","city":"Rheineck","country":"Schweiz"}]\' data-show-info-window-on-load=\'1\' data-mapoption-zoom=\'roadmap\' data-mapoption-map-type-id=\'\' data-mapoption-street-view-control=\'true\' data-mapoption-map-type-control=\'false\' data-mapoption-pan-control=\'false\' data-mapoption-scrollwheel=\'false\'></div>
                    </div>
                </div>';
    }

}
