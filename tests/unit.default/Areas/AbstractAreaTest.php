<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Codeception\Exception\ModuleException;
use DachcomBundle\Test\Test\DachcomBundleTestCase;
use DachcomBundle\Test\Util\VersionHelper;
use Pimcore\Document\Editable\EditableHandler;
use Pimcore\Model\Document\Tag\Area;
use Pimcore\Tests\Util\TestHelper;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

abstract class AbstractAreaTest extends DachcomBundleTestCase
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
        $info->getView()->getParameters()->add(['editmode' => false]);
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

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {

            $areaClass = 'Pimcore\Model\Document\Editable\Area';
            $infoClass = 'Pimcore\Model\Document\Editable\Area\Info';

            $area = new $areaClass();
            $info = new $infoClass();

        } else {

            $areaClass = 'Pimcore\Model\Document\Tag\Area';
            $infoClass = 'Pimcore\Model\Document\Tag\Area\Info';
            $viewModelClass = 'Pimcore\Templating\Model\ViewModel';

            $view = new $viewModelClass([
                'editmode' => false,
                'document' => $document
            ]);

            $area = new $areaClass();
            $info = new $infoClass();

            $info->setTag($area);
            $info->setView($view);

        }

        $area->setName($id);

        $info->setId($id);
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
        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $tagHandler = \Pimcore::getContainer()->get(EditableHandler::class);
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
