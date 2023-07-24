<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Numeric;
use Pimcore\Model\Document\Editable\Select;
use ToolboxBundle\Model\Document\Editable\GoogleMap;

class GoogleMapTest extends AbstractAreaTest
{
    public const TYPE = 'googleMap';

    public function testGoogleMapBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(3, $configElements);
        $this->assertEquals('numeric', $configElements[0]['type']);
        $this->assertEquals('map_zoom', $configElements[0]['name']);

        $this->assertEquals('select', $configElements[1]['type']);
        $this->assertEquals('map_type', $configElements[1]['name']);

        $this->assertEquals('checkbox', $configElements[2]['type']);
        $this->assertEquals('iw_on_init', $configElements[2]['name']);

    }

    public function testGoogleMapConfigParameter()
    {
        $configParam = $this->getToolboxConfig()->getAreaParameterConfig('googleMap');
        $this->assertEquals(
            [
                'map_options'    => [
                    'streetViewControl' => true,
                    'mapTypeControl'    => false,
                    'panControl'        => false,
                    'scrollwheel'       => false
                ],
                'map_style_url'  => false,
                'marker_icon'    => false,
                'map_api_key'    => '',
                'simple_api_key' => ''
            ],
            $configParam
        );
    }

    public function testGoogleMap()
    {
        $this->setupRequest();

        $locations = [
            [
                'street'  => 'Rorschacherstrasse 15',
                'zip'     => '9424',
                'city'    => 'Rheineck',
                'country' => 'Schweiz',
                'lat' => null,
                'lng' => null,
            ]
        ];

        $googleMapElement = new GoogleMap();
        $googleMapElement->setId('test');
        $googleMapElement->disableGoogleLookup();
        $googleMapElement->setDataFromEditmode($locations);

        $mapZoom = new Numeric();
        $mapZoom->setDataFromResource('5');

        $mapType = new Select();
        $mapType->setDataFromResource('roadmap');

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $iwOnInit = new Checkbox();
        $iwOnInit->setDataFromResource(1);

        $elements = [
            'googlemap'  => $googleMapElement,
            'map_zoom'   => $mapZoom,
            'map_type'   => $mapType,
            'iw_on_init' => $iwOnInit,
        ];

        $this->assertEquals(
            $this->filter($this->getCompare($locations)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testGoogleMapWidthAdditionalClass()
    {
        $this->setupRequest();

        $locations = [
            [
                'street'  => 'Rorschacherstrasse 15',
                'zip'     => '9424',
                'city'    => 'Rheineck',
                'country' => 'Schweiz',
                'lat'     => null,
                'lng'     => null,

            ]
        ];

        $googleMapElement = new GoogleMap();
        $googleMapElement->setId('test');
        $googleMapElement->disableGoogleLookup();
        $googleMapElement->setDataFromEditmode($locations);

        $mapZoom = new Numeric();
        $mapZoom->setDataFromResource('12');

        $mapType = new Select();
        $mapType->setDataFromResource('satellite');

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
            $this->filter($this->getCompareWithAdditionalClass($locations)),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare(array $locations)
    {
        return '<div class="toolbox-element toolbox-google-map ">
                    <div class="toolbox-google-map-container">
                        <div class="embed-responsive-item toolbox-googlemap" id="test" data-locations="' . htmlspecialchars(json_encode($locations), ENT_QUOTES, 'UTF-8') . '" data-show-info-window-on-load="1" data-mapoption-zoom="5" data-mapoption-map-type-id="roadmap" data-mapoption-street-view-control="true" data-mapoption-map-type-control="false" data-mapoption-pan-control="false" data-mapoption-scrollwheel="false"></div>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass(array $locations)
    {
        return '<div class="toolbox-element toolbox-google-map additional-class">
                    <div class="toolbox-google-map-container">
                        <div class="embed-responsive-item toolbox-googlemap" id="test" data-locations="' . htmlspecialchars(json_encode($locations), ENT_QUOTES, 'UTF-8') . '" data-show-info-window-on-load="1" data-mapoption-zoom="12" data-mapoption-map-type-id="satellite" data-mapoption-street-view-control="true" data-mapoption-map-type-control="false" data-mapoption-pan-control="false" data-mapoption-scrollwheel="false"></div>
                    </div>
                </div>';
    }

}
