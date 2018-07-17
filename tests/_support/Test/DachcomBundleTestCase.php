<?php

namespace DachcomBundle\Test\Test;

use Pimcore\Tests\Test\TestCase;
use DachcomBundle\Test\Helper\PimcoreBundle;

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

    /***
     * @return PimcoreBundle
     * @throws \Codeception\Exception\ModuleException
     */
    protected function getPimcoreBundle()
    {
        return $this->getModule('\\' . PimcoreBundle::class);
    }
}
