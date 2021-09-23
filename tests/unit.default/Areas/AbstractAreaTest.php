<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Codeception\Exception\ModuleException;
use Dachcom\Codeception\Test\BundleTestCase;
use Pimcore\Document\Editable\EditableHandler;
use Pimcore\Model\Document\Editable\Area;
use Pimcore\Tests\Util\TestHelper;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

abstract class AbstractAreaTest extends BundleTestCase
{
    /**
     * @return object|ConfigManager
     * @throws ModuleException
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
        $info->setParam('editmode', false);
        $info->getDocument()->setElements($documentElements);

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
        $info->setParam('editmode', true);

        $builder = $this->getContainer()->get(BrickConfigBuilder::class);
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL);

        $configNode = $configManager->getAreaConfig($info->getId());
        $themeOptions = $configManager->getConfig('theme');

        return $builder->buildDialogBoxConfiguration($info, $info->getId(), $configNode, $themeOptions)->getItems();
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

        $area = new Area();
        $area->setName($id);
        $area->setDocument($document);

        $info = new Area\Info();
        $info->setId($id);
        $info->setIndex(1);
        $info->setParams(array_merge($infoParams, ['editmode' => false]));
        $info->setEditable($area);

        return $info;
    }

    /**
     * @param Area\Info $info
     *
     * @return string
     */
    public function getAreaOutput(Area\Info $info)
    {
        $tagHandler = \Pimcore::getContainer()->get(EditableHandler::class);

        ob_start();
        $tagHandler->renderAreaFrontend($info);

        return ob_get_clean();
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
     * @throws ModuleException
     */
    public function setupRequest()
    {
        $request = Request::create('/');
        $requestStack = $this->getContainer()->get('request_stack');
        $requestStack->push($request);
    }
}
