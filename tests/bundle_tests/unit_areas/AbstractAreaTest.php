<?php

namespace DachcomBundle\Test\Unit;

use DachcomBundle\Test\Test\DachcomBundleTestCase;
use Pimcore\Model\Document\Tag\Area;
use Pimcore\Templating\Model\ViewModel;
use Pimcore\Tests\Util\TestHelper;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

abstract class AbstractAreaTest extends DachcomBundleTestCase
{
    /**
     * @return object|ConfigManager
     * @throws \Codeception\Exception\ModuleException
     */
    public function getToolboxConfig()
    {
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL);

        return $configManager;
    }

    /**
     * @param       $id
     * @param       $documentElements
     * @param array $infoParams
     *
     * @return string
     */
    public function generateRenderedArea($id, $documentElements, $infoParams = [])
    {
        $info = $this->generateAreaInfo($id, $infoParams);
        $info->getTag()->getView()->get('document')->setElements($documentElements);

        return $this->getAreaOutput($info);
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function generateBackendArea($id)
    {
        $info = $this->generateAreaInfo($id);

        $info->getView()->getParameters()->add(['editmode' => true]);

        $builder = $this->getContainer()->get(BrickConfigBuilder::class);
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL);

        $configNode = $configManager->getAreaConfig($info->getId());
        $themeOptions = $configManager->getConfig('theme');

        return $builder->buildElementConfigArguments($info->getId(), $info->getTag()->getName(), $info, $configNode, $themeOptions);
    }

    /**
     * @param       $id
     * @param array $infoParams
     *
     * @return Area\Info
     */
    public function generateAreaInfo($id, $infoParams = [])
    {
        $document = TestHelper::createEmptyDocumentPage('', true);

        $view = new ViewModel([
            'editmode' => false,
            'document' => $document
        ]);

        $area = new Area();
        $area->setView($view);
        $area->setName($id);

        $info = new Area\Info();
        $info->setId($id);
        $info->setTag($area);
        $info->setView($view);
        $info->setIndex(1);
        $info->setParams($infoParams);
        return $info;
    }

    /**
     * @param Area\Info $info
     *
     * @return string
     */
    public function getAreaOutput(Area\Info $info)
    {
        $tagHandler = \Pimcore::getContainer()->get('pimcore.document.tag.handler');

        ob_start();
        $tagHandler->renderAreaFrontend($info);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }

    /**
     * @param $output
     *
     * @return string
     */
    public function filter($output)
    {
        $output = preg_replace('/\r|\n/', '', $output);
        return trim(preg_replace('/(\>)\s*(\<)/m', '$1$2', $output));
    }

    /**
     * @throws \Codeception\Exception\ModuleException
     */
    public function setupRequest()
    {
        $request = Request::create('/');
        $requestStack = $this->getContainer()->get('request_stack');
        $requestStack->push($request);
    }
}
