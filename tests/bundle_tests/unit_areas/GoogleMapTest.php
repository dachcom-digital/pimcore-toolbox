<?php

namespace DachcomBundle\Test\Unit;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Numeric;
use Pimcore\Model\Document\Tag\Select;
use ToolboxBundle\Model\Document\Tag\GoogleMap;

class GoogleMapTest extends AbstractAreaTest
{
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
            $this->filter($this->generateRenderedArea('googleMap', $elements))
        );
    }

    public function testGoogleMapWidthAdditionalClasses()
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
            $this->filter($this->getCompareWithAdditionalClasses()),
            $this->filter($this->generateRenderedArea('googleMap', $elements))
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

    private function getCompareWithAdditionalClasses()
    {
        return '<div class="toolbox-element toolbox-google-map additional-class">
                    <div class="toolbox-google-map-container">
                        <div class="embed-responsive-item toolbox-googlemap" id="test" data-locations=\'[{"street":"Rorschacherstrasse 15","zip":"9424","city":"Rheineck","country":"Schweiz"}]\' data-show-info-window-on-load=\'1\' data-mapoption-zoom=\'roadmap\' data-mapoption-map-type-id=\'\' data-mapoption-street-view-control=\'true\' data-mapoption-map-type-control=\'false\' data-mapoption-pan-control=\'false\' data-mapoption-scrollwheel=\'false\'></div>
                    </div>
                </div>';
    }

}
