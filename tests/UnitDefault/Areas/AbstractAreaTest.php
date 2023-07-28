<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Codeception\Exception\ModuleException;
use Dachcom\Codeception\Support\Test\BundleTestCase;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Area;
use Pimcore\Tests\Support\Util\TestHelper;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Builder\BrickConfigBuilder;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

abstract class AbstractAreaTest extends BundleTestCase
{
    /**
     * @throws ModuleException
     */
    public function getToolboxConfig(): ConfigManager
    {
        return $this->getContainer()->get(ConfigManager::class);
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
        $area->setEditmode(false);

        $info = new Area\Info();
        $info->setId($id);
        $info->setIndex(1);
        $info->setParams(['editmode' => true]);
        $info->setEditable($area);

        $builder = $this->getContainer()->get(BrickConfigBuilder::class);
        $configManager = $this->getContainer()->get(ConfigManager::class);

        $configNode = $configManager->getAreaConfig($info->getId());
        $themeOptions = $configManager->getConfig('theme');

        return $builder->buildConfiguration($info, $info->getId(), $configNode, $themeOptions)->getItems();
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
