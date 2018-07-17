<?php

namespace DachcomBundle\Test\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\ModuleContainer;
use ToolboxBundle\Tool\Install;

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

        $this->debug('[TOOLBOX] Running toolbox installer');

        // install dachcom bundle
        $installer = $pimcoreModule->getContainer()->get(Install::class);
        $installer->install();
    }
}
