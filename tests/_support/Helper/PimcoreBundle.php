<?php

namespace DachcomBundle\Test\Helper;

use Pimcore\Event\TestEvents;
use Pimcore\Tests\Helper\Pimcore;

class PimcoreBundle extends Pimcore
{
    /**
     * Initialize the kernel (see parent Symfony module)
     */
    protected function initializeKernel()
    {
        $maxNestingLevel = 200; // Symfony may have very long nesting level
        $xdebugMaxLevelKey = 'xdebug.max_nesting_level';
        if (ini_get($xdebugMaxLevelKey) < $maxNestingLevel) {
            ini_set($xdebugMaxLevelKey, $maxNestingLevel);
        }

        $this->kernel = require_once __DIR__ . '/../../kernelBuilder.php';
        $this->kernel->boot();

        $this->setupPimcoreDirectories();

        if ($this->config['cache_router'] === true) {
            $this->persistService('router', true);
        }

        // dispatch kernel booted event - will be used from services which need to reset state between tests
        $this->kernel->getContainer()->get('event_dispatcher')->dispatch(TestEvents::KERNEL_BOOTED);
    }
}
