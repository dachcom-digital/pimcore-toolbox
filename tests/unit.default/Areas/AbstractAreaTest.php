<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Codeception\Exception\ModuleException;
use Dachcom\Codeception\Test\BundleTestCase;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Model\Document\Editable;
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
     * @param string $id
     * @param array  $editables
     * @param array  $infoParams
     *
     * @return string
     */
    public function generateRenderedArea($id, $editables, $infoParams = [])
    {
        $index = 1;

        $document = TestHelper::createEmptyDocumentPage('', false);
        $document->setMissingRequiredEditable(false);

        $blockData = [];
        $blockData[] = [
            'key'    => $index,
            'type'   => $id,
            'hidden' => false
        ];

        $infoParams['document'] = $document;
        $infoParams['editmode'] = false;

        $area = new Editable\Areablock();
        $area->setName('test');
        $area->setRealName('test');
        $area->setDocument($document);
        $area->setEditmode(false);
        $area->setDataFromResource(serialize($blockData));
        $area->setConfig([
            'indexes'      => [],
            'globalParams' => $infoParams
        ]);

        $editables[] = $area;

        $indexedEditables = [];

        foreach ($editables as $editableName => $editable) {

            if ($editable instanceof Editable\Areablock) {
                $indexedEditables[$editable->getName()] = $editable;
                continue;
            }

            $key = sprintf('test:%d.%s', $index, $editableName);

            $editable->setName($key);
            $indexedEditables[$key] = $editable;
        }

        $document->setEditables($indexedEditables);

        //$this->getContainer()->get(BlockStateStack::class)->loadArray([['blocks' => [], 'indexes' => []]]);

        //$this->getContainer()->get('request_stack')->getCurrentRequest()->attributes->set(DynamicRouter::CONTENT_KEY, $document);
        //$this->getContainer()->get('request_stack')->getMainRequest()->attributes->set(DynamicRouter::CONTENT_KEY, $document);
        //$this->getContainer()->get(DocumentResolver::class)->setDocument($this->getContainer()->get('request_stack')->getCurrentRequest(), $document);
        //$this->getContainer()->get(DocumentResolver::class)->setDocument($this->getContainer()->get('request_stack')->getMainRequest(), $document);

        return $area->renderIndex(0, true);
    }

    /**
     * @param string $id
     *
     * @return EditableDialogBoxConfiguration
     * @throws \Exception
     */
    public function generateBackendArea($id)
    {
        $document = TestHelper::createEmptyDocumentPage('', false);
        $document->setMissingRequiredEditable(false);

        $area = new Area();
        $area->setName($id);
        $area->setDocument($document);

        $info = new Area\Info();
        $info->setId($id);
        $info->setIndex(1);
        $info->setParams(['editmode' => true]);
        $info->setEditable($area);

        $builder = $this->getContainer()->get(BrickConfigBuilder::class);
        $configManager = $this->getContainer()->get(ConfigManager::class);
        $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL);

        $configNode = $configManager->getAreaConfig($info->getId());
        $themeOptions = $configManager->getConfig('theme');

        return $builder->buildDialogBoxConfiguration($info, $info->getId(), $configNode, $themeOptions)->getItems();
    }

    /**
     * @param string $output
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
