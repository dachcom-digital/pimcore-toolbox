<?php

namespace DachcomBundle\Test\UnitContext;

use DachcomBundle\Test\Test\DachcomBundleTestCase;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Manager\AreaManager;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

class ContextTest extends DachcomBundleTestCase
{
    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testAssureCurrentContext()
    {
        $this->setupRequest();

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $this->assertEquals(true, $configManager->isContextConfig());
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testCurrentContext()
    {
        $this->setupRequest();

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $this->assertEquals('context_a', $configManager->getContextIdentifier());
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testContextAConfiguration()
    {
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('context_element');

        $this->assertEquals(['headline'], $areaConfig['allowed']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testContextBConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'context_b']);

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('context_element');

        $this->assertEquals(['headline'], $areaConfig['allowed']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testContextDisabledMergeWithRootConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'context_c']);

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('context_element');

        $this->assertEquals(['content'], $areaConfig['allowed']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testCkEditorSettingsOnNoneContextConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'disabled']);

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $ckEditorSettings = $configManager->getConfig('ckeditor');

        $this->assertArrayHasKey('config', $ckEditorSettings);
        $this->assertArrayHasKey('global_style_sets', $ckEditorSettings);
        $globalStyleSets = $ckEditorSettings['global_style_sets'];

        $this->assertArrayHasKey('default', $globalStyleSets);

        $data = [
            [
                'name'       => 'Lead Global',
                'element'    => 'p',
                'attributes' => ['class' => 'lead'],

            ]
        ];

        $this->assertEquals($data, $globalStyleSets['default']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testCkEditorSettingsOnContextConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'context_a']);

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $ckEditorSettings = $configManager->getConfig('ckeditor');

        $this->assertArrayHasKey('config', $ckEditorSettings);
        $this->assertArrayHasKey('global_style_sets', $ckEditorSettings);
        $globalStyleSets = $ckEditorSettings['global_style_sets'];

        $this->assertArrayHasKey('default', $globalStyleSets);

        $data = [
            [
                'name'       => 'Lead For Portal1',
                'element'    => 'p',
                'attributes' => ['class' => 'lead-portal']
            ],
            [
                'name'       => 'Dark Grey',
                'element'    => 'h1',
                'attributes' => ['class' => 'grey-1']
            ]
        ];

        $this->assertEquals($data, $globalStyleSets['default']);
    }

    /**
     * @param array $query
     *
     * @throws \Codeception\Exception\ModuleException
     */
    private function setupRequest($query = [])
    {
        $request = Request::create('/');
        $request->query->add($query);
        $requestStack = $this->getContainer()->get('request_stack');
        $requestStack->push($request);
    }
}
