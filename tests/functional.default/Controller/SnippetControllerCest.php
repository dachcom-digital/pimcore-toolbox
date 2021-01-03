<?php

namespace DachcomBundle\Test\FunctionalDefault\Controller;

use DachcomBundle\Test\FunctionalTester;
use Pimcore\Model\Document;

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
        $combo = new Document\Tag\Select();
        $combo->setDataFromResource('default');
        $combo->setName('ts_type');

        $snippetParams = [
            'module'     => 'ToolboxBundle',
            'controller' => 'Snippet',
            'action'     => 'teaser'
        ];

        $I->haveAUserWithAdminRights('dachcom_test');
        $I->amLoggedInAs('dachcom_test');
        $I->haveASnippet('snippet-test', $snippetParams, ['ts_type' => $combo]);

        $I->amOnPage('/snippet-test?pimcore_editmode=true');

        $I->seeElement('.single-teaser.default');
        $I->seeElement('.teaser-headline');
        $I->seeElement('.teaser-text');
        $I->seeElement('.pimcore_tag_link');
    }
}
