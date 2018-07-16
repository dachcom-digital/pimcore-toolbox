<?php

namespace Toolbox\Test\Test;

use Pimcore\Tests\Test\TestCase;
use Toolbox\Test\Helper\PimcoreBundle;

abstract class ToolboxTestCase extends TestCase
{
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     * @throws \Codeception\Exception\ModuleException
     */
    protected function getContainer()
    {
        /** @var \Pimcore $pimcoreModule */
        $pimcoreModule = $this->getModule('\\' . PimcoreBundle::class);
        return $pimcoreModule->getContainer();
    }
}
