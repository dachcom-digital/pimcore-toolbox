<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Lib\ModuleContainer;
use Pimcore\Event\TestEvents;
use Pimcore\Tests\Helper\Pimcore;
use Symfony\Component\Filesystem\Filesystem;

class PimcoreBundle extends Pimcore
{
    /**
     * @inheritDoc
     */
    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        $this->config = array_merge($this->config, [
            // set specific configuration file for container
            'configuration_file' => null
        ]);

        parent::__construct($moduleContainer, $config);
    }

    public function _initialize()
    {
        $isNew = \Pimcore::getKernel() === null;

        parent::_initialize();

        if ($isNew === true) {
            return;
        }

        $this->initializeKernel();

    }

    /**
     * Initialize the kernel (see parent Pimcore module)
     */
    protected function initializeKernel()
    {
        $maxNestingLevel = 200; // Symfony may have very long nesting level
        $xdebugMaxLevelKey = 'xdebug.max_nesting_level';
        if (ini_get($xdebugMaxLevelKey) < $maxNestingLevel) {
            ini_set($xdebugMaxLevelKey, $maxNestingLevel);
        }

        if ($this->config['configuration_file'] !== null) {
            putenv('DACHCOM_BUNDLE_CONFIG_FILE=' . $this->config['configuration_file']);
        } else {
            putenv('DACHCOM_BUNDLE_CONFIG_FILE');
        }

        //touch cache container to force refresh
        $fileSystem = new Filesystem();
        $this->kernel = require __DIR__ . '/../../kernelBuilder.php';

        $fileSystem->remove($this->kernel->getCacheDir());
        $fileSystem->mkdir($this->kernel->getCacheDir());

        $this->kernel->boot();

        $this->setupPimcoreDirectories();

        if ($this->config['cache_router'] === true) {
            $this->persistService('router', true);
        }

        // dispatch kernel booted event - will be used from services which need to reset state between tests
        $this->kernel->getContainer()->get('event_dispatcher')->dispatch(TestEvents::KERNEL_BOOTED);
    }
}
