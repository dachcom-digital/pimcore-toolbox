<?php

namespace DachcomBundle\Test\UnitTheme;

use Dachcom\Codeception\Support\Test\BundleTestCase;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Manager\AreaManager;
use ToolboxBundle\Manager\AreaManagerInterface;

class AreaManagerTest extends BundleTestCase
{
    /**
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
        $requestStack = $this->getContainer()->get('request_stack');
        $requestStack->push($request);
    }
}
