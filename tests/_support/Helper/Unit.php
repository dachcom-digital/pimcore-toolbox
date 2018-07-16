<?php

namespace Toolbox\Test\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\ModuleContainer;
use Toolbox\Test\Helper\PimcoreBundle;
use ToolboxBundle\Tool\Install;
use Pimcore\Tests\Util\Autoloader;

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

        $this->debug('[TOOLBOX] Running toolbox framework installer');

        // install toolbox
        $installer = $pimcoreModule->getContainer()->get(Install::class);
        //$installer->install();

        //explicitly load installed classes so that the new ones are used during tests
        //Autoloader::load(OnlineShopTaxClass::class);

    }
}
