<?php

namespace Toolbox\Test\PHPUnit\Suites;

use Symfony\Component\HttpFoundation\Request;
use Toolbox\Test\Base;
use ToolboxBundle\Manager\AreaManagerInterface;

class AreaManager extends Base
{

    public function testAreaBlockAppearanceDisallowConfiguration()
    {
        $this->printTestName();
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->get(\ToolboxBundle\Manager\AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('disallowed_content');

        $this->assertNotContains('image', $areaConfig['allowed']);
    }

    public function testAreaBlockAppearanceAllowConfiguration()
    {
        $this->printTestName();
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->get(\ToolboxBundle\Manager\AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('allowed_content');

        $this->assertContains('image', $areaConfig['allowed']);
    }

    public function testAreaBlockAppearanceMixedConfiguration()
    {
        $this->printTestName();
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->get(\ToolboxBundle\Manager\AreaManager::class);
        $areaConfig = $areaManager->getAreaBlockConfiguration('mixed_content');

        $this->assertContains('image', $areaConfig['allowed']);
    }

    public function testAreaBlockAppearanceDisallowedInSnippetConfiguration()
    {
        $this->printTestName();
        $this->setupRequest();

        /** @var AreaManagerInterface $areaManager */
        $areaManager = $this->get(\ToolboxBundle\Manager\AreaManager::class);
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
