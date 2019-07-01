<?php

namespace DachcomBundle\Test\AcceptanceDefault;

use DachcomBundle\Test\AcceptanceTester;

class AreaPositionCest
{
    /**
     * @param AcceptanceTester $I
     *
     * @throws \Exception
     */
    public function testAreaButtonsPosition(AcceptanceTester $I)
    {
        $I->haveAUserWithAdminRights('backendTester');

        $document = $I->haveAPageDocument('toolbox-area-test');
        $I->seeAToolboxAreaElementPlacedOnDocument($document);

        $I->amOnPage(sprintf('/admin/login/deeplink?document_%d_page', $document->getId()));
        $I->submitForm('form', ['username' => 'backendTester', 'password' => 'backendTester']);

        // wait for pimcore gui
        $I->waitForElement('div#pimcore_panel_tree_objects', 20);
        // wait for document gui
        $I->waitForElement('div.x-panel .x-tab-inner', 20);
        // switch to document edit iframe
        $I->switchToIFrame('document_iframe_' . $document->getId());
        // wait for input element
        $I->waitForElement('.toolbox-element.toolbox-headline .pimcore_tag_input', 10);
        // click "add element above" symbol in area buttons block
        $I->clickWithLeftButton('.toolbox-element.toolbox-headline .pimcore_tag_input', 5, -75);
        // i want to see an dropdown containing "accordion"
        $I->waitForText('Accordion', 10, '.x-menu .x-menu-item');
    }
}
