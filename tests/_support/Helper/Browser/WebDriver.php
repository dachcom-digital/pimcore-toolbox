<?php

namespace DachcomBundle\Test\Helper\Browser;

use Codeception\Module;

class WebDriver extends Module\WebDriver
{
    /**
     * Actor Function to see a page with enabled edit-mode
     *
     * @param string $page
     */
    public function amOnPageInEditMode(string $page)
    {
        $this->amOnPage(sprintf('%s?pimcore_editmode=true', $page));
    }
}
