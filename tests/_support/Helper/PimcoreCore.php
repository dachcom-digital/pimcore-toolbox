<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Lib\ModuleContainer;
use Pimcore\Cache;
use Pimcore\Config;
use Pimcore\Event\TestEvents;
use Pimcore\Tests\Helper\Pimcore as PimcoreCoreModule;
use Symfony\Component\Filesystem\Filesystem;

class PimcoreCore extends PimcoreCoreModule
{
    /**
     * @inheritDoc
     */
    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        $this->config = array_merge($this->config, [
            // set specific configuration file for suite
            'configuration_file' => null
        ]);

        parent::__construct($moduleContainer, $config);
    }

    public function _initialize()
    {
        Config::setEnvironment($this->config['environment']);

        $this->initializeKernel();

        // connect and initialize DB
        $this->setupDbConnection();

        // disable cache
        Cache::disable();
    }

    protected function initializeKernel()
    {
        $maxNestingLevel = 200; // Symfony may have very long nesting level
        $xdebugMaxLevelKey = 'xdebug.max_nesting_level';
        if (ini_get($xdebugMaxLevelKey) < $maxNestingLevel) {
            ini_set($xdebugMaxLevelKey, $maxNestingLevel);
        }

        $configFile = null;
        if ($this->config['configuration_file'] !== null) {
            $configFile = $this->config['configuration_file'];
        } else {
            $configFile = 'config_default.yml';
        }

        $this->bootKernelWithConfiguration($configFile);

        $this->setupPimcoreDirectories();
    }

    /**
     * @param string $configuration
     */
    public function bootKernelWithConfiguration($configuration)
    {
        putenv('DACHCOM_BUNDLE_CONFIG_FILE=' . $configuration);

        $this->clearCache();

        $this->kernel = require __DIR__ . '/../../kernelBuilder.php';
        $this->getKernel()->boot();

        if ($this->config['cache_router'] === true) {
            $this->persistService('router', true);
        }

        // dispatch kernel booted event - will be used from services which need to reset state between tests
        $this->kernel->getContainer()->get('event_dispatcher')->dispatch(TestEvents::KERNEL_BOOTED);
    }

    /**
     * @param bool $force
     */
    public function clearCache($force = true)
    {
        $fileSystem = new Filesystem();

        try {
            $fileSystem->remove(PIMCORE_PROJECT_ROOT . '/var/cache');
            $fileSystem->mkdir(PIMCORE_PROJECT_ROOT . '/var/cache');
        } catch (\Exception $e) {
            //try again later if "directory not empty" error occurs.
            if ($force === true) {
                sleep(1);
                $this->clearCache(false);
            }
        }
    }
}
