<?php

namespace Toolbox\Test\Unit;

use Symfony\Component\HttpFoundation\Request;
use Toolbox\Test\Test\ToolboxTestCase;
use ToolboxBundle\Manager\AreaManager;
use ToolboxBundle\Manager\AreaManagerInterface;

class AreaManagerTest extends ToolboxTestCase
{
    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testAreaBlockAppearanceDisallowConfiguration()
    {
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('disallowed_content');

        $this->assertNotContains('image', $areaConfig['allowed']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testAreaBlockAppearanceAllowConfiguration()
    {
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('allowed_content');

        $this->assertContains('image', $areaConfig['allowed']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testAreaBlockAppearanceMixedConfiguration()
    {
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('mixed_content');

        $this->assertContains('image', $areaConfig['allowed']);
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function testAreaBlockAppearanceDisallowedInSnippetConfiguration()
    {
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->getContainer()->get(AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('disallowed_content', true);

        $this->assertNotContains('headline', $areaConfig['allowed']);
    }

    private function setupRequest()
    {
        $request = Request::create('/');
        $requestStack = \Pimcore::getContainer()->get('request_stack');
        $requestStack->push($request);
    }
}
