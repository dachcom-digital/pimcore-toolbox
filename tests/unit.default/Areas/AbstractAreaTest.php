<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Codeception\Exception\ModuleException;
use Dachcom\Codeception\Test\BundleTestCase;
use Dachcom\Codeception\Util\VersionHelper;
use Pimcore\Document\Editable\EditableHandlerInterface;
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
        $info->getEditable()->getView()->get('document')->setElements($documentElements);

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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $areaClass = '\Pimcore\Model\Document\Editable\Area';
            $infoClass = '\Pimcore\Model\Document\Editable\Area\Info';
        } else {
            $areaClass = '\Pimcore\Model\Document\Editable\Area';
            $infoClass = '\Pimcore\Model\Document\Editable\Area\Info';
        }

        $view = new \Pimcore\Templating\Model\ViewModel([
            'editmode' => false,
            'document' => $document
        ]);

        $area = new $areaClass();
        $area->setName($id);
        $area->setView($view);

        $info = new $infoClass();
        $info->setId($id);
        $info->setIndex(1);
        $info->setParams($infoParams);
        $info->setTag($area);
        $info->setView($view);

        return $info;
    }

    /**
     * @param Area\Info $info
     *
     * @return string
     */
    public function getAreaOutput(Area\Info $info)
    {
        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $tagHandler = \Pimcore::getContainer()->get(EditableHandlerInterface::class);
        } else {
            $tagHandler = \Pimcore::getContainer()->get('pimcore.document.tag.handler');
        }

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
     * @throws ModuleException
     */
    public function setupRequest()
    {
        $request = Request::create('/');
        $requestStack = $this->getContainer()->get('request_stack');
        $requestStack->push($request);
    }
}
