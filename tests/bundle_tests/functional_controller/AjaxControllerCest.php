<?php

namespace DachcomBundle\Test\Functional;

use DachcomBundle\Test\FunctionalTester;

class AjaxControllerCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testGoogleMapInfoWindow(FunctionalTester $I)
    {
        $query = [
            'mapParams' => [
                'title'   => 'test title',
                'street'  => 'test street',
                'zip'     => 'test zip',
                'city'    => 'test city',
                'add'     => 'test additional text',
                'country' => 'test country',
                'lat'     => 0,
                'lng'     => 0,
            ],
            'locale'    => 'en'
        ];

        $I->amOnPage(sprintf('/toolbox/ajax/gm-info-window?%s', http_build_query($query)));

        $I->seeElement('.info-window');
        $I->seeElement('.google-map-route-planer');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testVideoTypesRequest(FunctionalTester $I)
    {
        $I->amOnPage('/toolbox/ajax/video-allowed-video-types');
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'name'   => 'youtube',
            'value'  => 'youtube',
            'config' => [
                'allow_lightbox' => true,
                'id_label'       => null
            ]
        ]);
    }
}
