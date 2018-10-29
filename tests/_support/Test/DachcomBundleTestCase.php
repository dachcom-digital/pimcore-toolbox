<?php

namespace DachcomBundle\Test\Test;

use DachcomBundle\Test\Helper\PimcoreCore;
use Pimcore\Tests\Test\TestCase;

abstract class DachcomBundleTestCase extends TestCase
{
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     * @throws \Codeception\Exception\ModuleException
     */
    protected function getContainer()
    {
        return $this->getPimcoreBundle()->getContainer();
    }

    /**
     * @return PimcoreCore
     * @throws \Codeception\Exception\ModuleException
     */
    protected function getPimcoreBundle()
    {
        return $this->getModule('\\' . PimcoreCore::class);
    }
}
