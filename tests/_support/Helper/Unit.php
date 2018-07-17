<?php

namespace DachcomBundle\Test\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\ModuleContainer;

class Unit extends \Codeception\Module
{
    /**
     * @inheritDoc
     */
    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        $this->config = array_merge($this->config, [
            'run_installer' => true
        ]);

        parent::__construct($moduleContainer, $config);
    }

    /**
     * @param array $settings
     * @throws \Codeception\Exception\ModuleException
     */
    public function _beforeSuite($settings = [])
    {
        if (!$this->config['run_installer']) {
            return;
        }

        /** @var PimcoreBundle $pimcoreModule */
        $pimcoreModule = $this->getModule('\\' . PimcoreBundle::class);

        $bundleName = getenv('DACHCOM_BUNDLE_NAME');
        $installerClass = getenv('DACHCOM_BUNDLE_INSTALLER_CLASS');

        $this->debug(sprintf('[%s] Running installer...', strtoupper($bundleName)));

        // install dachcom bundle
        $installer = $pimcoreModule->getContainer()->get($installerClass);
        $installer->install();
    }
}
