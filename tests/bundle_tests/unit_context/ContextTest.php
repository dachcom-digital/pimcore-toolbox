<?php

namespace DachcomBundle\Test\Unit;

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
