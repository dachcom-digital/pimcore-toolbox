<?php

namespace DachcomBundle\Test\FunctionalDefault\Controller;

use DachcomBundle\Test\FunctionalTester;

class SnippetControllerCest
{
    /**
     * @param FunctionalTester $I
     */
    public function testSnippetController(FunctionalTester $I)
    {
        $snippetParams = [
            'module'     => 'ToolboxBundle',
            'controller' => 'Snippet',
            'action'     => 'teaser'
        ];

        $I->haveAUserWithAdminRights('dachcom_test');
        $I->amLoggedInAs('dachcom_test');
        $I->haveASnippet('snippet-test', $snippetParams);

        $I->amOnPage('/snippet-test?pimcore_editmode=true');

        $I->seeElement('.snippet-selector');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testSnippetWithDefaultTeaserController(FunctionalTester $I)
    {
        $snippetParams = [
            'module'     => 'ToolboxBundle',
            'controller' => 'Snippet',
            'action'     => 'teaser'
        ];

        $snippetEditables = [
            'ts_type'        => [
                'type'             => 'select',
                'dataFromResource' => 'default',
            ],
            'use_light_box'  => [
                'type' => 'Checkbox',
            ],
            'ts_add_classes' => [
                'type' => 'select',
            ],
            'link'           => [
                'type' => 'link',
            ],
            'image'          => [
                'type' => 'image',
            ],
            'headline'       => [
                'type' => 'input',
            ],
            'text'           => [
                'type' => 'wysiwyg',
            ],
        ];

        $document = $I->haveASnippet('snippet-test', $snippetParams);

        $I->seeEditablesPlacedOnDocument($document, $snippetEditables);

        $I->haveAUserWithAdminRights('dachcom_test');
        $I->amLoggedInAs('dachcom_test');
        $I->amOnPage('/snippet-test?pimcore_editmode=true');

        $I->seeElement('.single-teaser.default');
        $I->seeElement('.teaser-headline');
        $I->seeElement('.teaser-text');
        $I->seeElement('.pimcore_tag_link');
    }
}
