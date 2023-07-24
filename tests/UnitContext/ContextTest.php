<?php

namespace DachcomBundle\Test\UnitContext;

use Dachcom\Codeception\Support\Test\BundleTestCase;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Manager\AreaManager;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

class ContextTest extends BundleTestCase
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
    public function testWysiwygEditorSettingsOnNoneContextConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'disabled']);

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $wysiwygSettings = $configManager->getConfig('wysiwyg_editor');

        $this->assertArrayHasKey('config', $wysiwygSettings);
        $this->assertArrayHasKey('style_formats', $wysiwygSettings['config']);
        $styleFormats = $wysiwygSettings['config']['style_formats'];

        $data = [
            [
                'title'    => 'Lead Global',
                'selector' => 'p',
                'classes'  => 'lead',

            ]
        ];

        $this->assertEquals($data, $styleFormats);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testWysiwygEditorSettingsOnContextConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'context_a']);

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $wysiwygSettings = $configManager->getConfig('wysiwyg_editor');

        $this->assertArrayHasKey('config', $wysiwygSettings);
        $this->assertArrayHasKey('style_formats', $wysiwygSettings['config']);
        $styleFormats = $wysiwygSettings['config']['style_formats'];

        $data = [
            [
                'title'    => 'Lead For Portal1',
                'selector'  => 'p',
                'classes' => 'lead-portal'
            ],
            [
                'title'    => 'Dark Grey',
                'selector'  => 'h1',
                'classes' => 'grey-1'
            ]
        ];

        $this->assertEquals($data, $styleFormats);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testThemeGridOnContextConfiguration()
    {
        $this->setupRequest(['mock_toolbox_context' => 'context_c']);

        /** @var ConfigManagerInterface $configManager */
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $themeSettings = $configManager->getConfig('theme');

        $this->assertArrayHasKey('grid', $themeSettings);
        $this->assertArrayHasKey('grid_size', $themeSettings['grid']);
        $this->assertArrayHasKey('breakpoints', $themeSettings['grid']);

        $this->assertEquals(8, $themeSettings['grid']['grid_size']);
        $this->assertCount(2, $themeSettings['grid']['breakpoints']);

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
