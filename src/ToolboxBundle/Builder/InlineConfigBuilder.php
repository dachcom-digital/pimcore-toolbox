<?php

namespace ToolboxBundle\Builder;

use Pimcore\Extension\Document\Areabrick\Exception\ConfigurationException;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Block;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Document\Editable\EditableWorker;
use ToolboxBundle\Document\Response\HeadlessResponse;

class InlineConfigBuilder extends AbstractConfigBuilder implements InlineConfigBuilderInterface
{
    protected EditableWorker $editableWorker;

    public function setEditableWorker(EditableWorker $editableWorker)
    {
        $this->editableWorker = $editableWorker;
    }

    public function buildInlineConfiguration(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $editMode = false): string
    {
        $configurationView = [];
        $inlineConfigElements = $areaConfig['inline_config_elements'] ?? [];

        $items = $this->parseConfigElements($info, $brickId, $themeOptions, $inlineConfigElements, [], false);

        foreach ($items as $item) {
            $configurationView[] = $this->templating->render(
                $this->templating->resolveTemplate([
                    sprintf('@Toolbox/admin/inline_config/editable_%s.html.twig', $item['type']),
                    '@Toolbox/admin/inline_config/editable.html.twig'
                ]),
                [
                    'item'     => $item,
                    'editable' => $this->buildEditable($info, $item, $editMode)
                ]
            );
        }

        if (count($configurationView) === 0) {
            return '';
        }

        return sprintf('<div class="inline-config-area">%s</div>', implode(PHP_EOL, $configurationView));
    }

    public function buildInlineConfigurationData(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = []): array
    {
        $data = [];
        $inlineConfigElements = $areaConfig['inline_config_elements'] ?? [];

        foreach ($inlineConfigElements as $itemName => $itemData) {

            $item = [
                'name'   => $itemName,
                'type'   => $itemData['type'],
                'config' => $itemData['config'] ?? []
            ];

            if (array_key_exists('children', $itemData)) {
                foreach ($itemData['children'] as $childName => $childData) {
                    $item['children'][] = [
                        'name'   => $childName,
                        'type'   => $childData['type'],
                        'config' => $childData['config'] ?? []
                    ];
                }
            }

            $editableData = $this->buildEditable($info, $item, false);

            $data[$itemName] = $editableData;
        }

        return $data;
    }

    private function buildEditable(Info $info, array $item, bool $editMode): Editable|string|array
    {
        if ($item['type'] === 'block') {
            return $this->buildBlockEditable($info, $item['name'], $item['config'], $item['children'] ?? [], $editMode);
        }

        if ($item['type'] === 'areablock') {
            return $this->buildAreaBlockEditable($info, $item['name'], $item['config'], $editMode);
        }

        if ($item['type'] === 'column') {
            return $this->buildColumnEditable($info, $item['config'], $editMode);
        }

        return $this->buildStandardEditable($info, $item['type'], $item['name'], $item['config'], $editMode);
    }

    private function buildStandardEditable(Info $info, string $type, string $inputName, array $config, bool $editMode): Editable|string|array
    {
        return $this->processEditable($info->getDocument(), $type, $inputName, $config, $editMode);
    }

    private function buildColumnEditable(Info $info, array $config, bool $editMode): string|array
    {
        $data = [];
        $document = $info->getDocument();
        $columns = $info->getParam('columns');

        if ($columns === null) {
            throw new ConfigurationException(
                sprintf(
                    'Cannot render "columns" editable. Brick "%s" does not provide any "columns" definitions in $info parameter',
                    $info->getId()
                )
            );
        }

        foreach ($columns as $column) {

            $areaBlockDataResponse = null;
            $columnName = sprintf('c%s', $column['name']);

            $config['areablock_config_name'] = $info->getId();

            ob_start();

            echo $this->processEditable($document, 'areablock', $columnName, $config, $editMode, true);

            if ($editMode === false) {
                $areaBlockDataResponse = $this->processEditable($document, 'areablock', $columnName, $config, false);
            }

            $areaBlockHtmlResponse = ob_get_clean();

            $data[$columnName] = $editMode
                ? sprintf('<div class="%s"><div class="%s">%s</div></div>', $column['columnClass'], $column['innerClass'], $areaBlockHtmlResponse)
                : $areaBlockDataResponse;
        }

        return $editMode ? implode(PHP_EOL, $data) : $data;
    }

    private function buildAreaBlockEditable(Info $info, string $inputName, array $config, bool $editMode): string|array
    {
        $areaBlockDataResponse = '';
        $document = $info->getDocument();

        $config['areablock_config_name'] = $info->getId();

        ob_start();

        echo $this->processEditable($document, 'areablock', $inputName, $config, $editMode, true);

        if ($editMode === false) {
            $areaBlockDataResponse = $this->processEditable($document, 'areablock', $inputName, $config, false);
        }

        $areaBlockHtmlResponse = ob_get_clean();

        return $editMode ? $areaBlockHtmlResponse : $areaBlockDataResponse;
    }

    private function buildBlockEditable(Info $info, string $inputName, array $config, array $blockElements, bool $editMode): string|array
    {
        $data = [];
        $document = $info->getDocument();

        ob_start();

        if (!array_key_exists('default', $config)) {
            $config['default'] = 1;
        }

        /** @var Block $blockEditable */
        $blockEditable = $this->editableRenderer->getEditable($document, 'block', $inputName, $config, $editMode);

        foreach ($blockEditable->getIterator() as $blockIndex) {
            foreach ($blockElements as $blockElement) {

                $beType = $blockElement['type'];
                $beName = $blockElement['name'];
                $beConfig = $blockElement['config'];

                echo $this->processEditable($document, $beType, $beName, $beConfig, $editMode, true);

                if ($editMode === false) {
                    $data[] = $this->processEditable($document, $beType, $beName, $beConfig, false, false, $info->getId());
                }
            }
        }

        $areaBlockHtmlResponse = ob_get_clean();

        return $editMode ? $areaBlockHtmlResponse : $data;
    }

    private function processEditable(
        PageSnippet $document,
        string $type,
        string $name,
        array $config,
        bool $editMode,
        bool $forceRendering = false,
        ?string $brickParent = null,
    ): mixed {

        $isSimple = !$this->isBlockEditable($type);

        // override config with area block config
        if ($type === 'areablock' && $editMode === true) {

            $areaBlockConfigurationName = $name;
            if (array_key_exists('areablock_config_name', $config)) {
                $areaBlockConfigurationName = $config['areablock_config_name'];
                unset($config['areablock_config_name']);
            }

            $config = $this->areaManager->getAreaBlockConfiguration($areaBlockConfigurationName, $document instanceof Snippet, true);
        }

        /** @var Editable $editable */
        $editable = $this->editableRenderer->getEditable($document, $type, $name, $config, $editMode);

        if ($isSimple === true && $brickParent !== null) {

            if ($editMode === false) {

                $simpleHeadlessResponse = new HeadlessResponse(HeadlessResponse::TYPE_EDITABLE, $brickParent);
                $simpleHeadlessResponse->setInlineConfigElementData([$editable->getRealName() => $editable]);

                $this->editableWorker->processEditable($simpleHeadlessResponse, $editable);

                return $editable;
            }

            return $editable->render();
        }

        if ($forceRendering === false && $editMode === false) {
            return $editable;
        }

        // simple editables output can be returned
        if ($isSimple === true) {
            return $editable->render();
        }

        echo $editable->render();

        return '';
    }

    private function isBlockEditable(string $type): bool
    {
        return in_array($type, ['area', 'block', 'areablock', 'scheduledblock'], true);
    }
}
