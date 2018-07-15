<?php

namespace Toolbox\Test;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class SuiteBase.
 */
class SuiteBase extends KernelTestCase
{
    /**
     * Setup.
     */
    protected function setUp()
    {
        AbstractObject::setHideUnpublished(false);
    }

    /**
     * Tear Down.
     */
    protected function tearDown()
    {
    }
}
