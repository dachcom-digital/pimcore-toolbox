<?php

namespace ToolboxBundle\Builder;

use Pimcore\Extension\Document\Areabrick\Exception\ConfigurationException;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Block;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Document\Snippet;
use Symfony\Component\EventDispatcher\GenericEvent;
use ToolboxBundle\Document\Editable\EditableWorker;
use ToolboxBundle\Document\Response\HeadlessResponse;

class InlineConfigBuilder extends AbstractConfigBuilder implements InlineConfigBuilderInterface
{
    public function buildInlineConfiguration(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $editMode = false): string
    {
        $configurationView = [];
        $inlineConfigElements = $areaConfig['inline_config_elements'] ?? [];

        $items = $this->parseConfigElements($info, $brickId, $themeOptions, $inlineConfigElements, []);

        foreach ($items as $item) {
            $configurationView[] = $this->templating->render(
                '@Toolbox/admin/inline_config/editable.html.twig',
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

    private function buildEditable(Info $info, array $item, bool $editMode): string|array
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

    private function buildStandardEditable(Info $info, string $type, string $inputName, array $options, bool $editMode): string|array
    {
        return $this->processEditable($info->getDocument(), $type, $inputName, $options, $editMode);
    }

    private function buildColumnEditable(Info $info, array $options, bool $editMode): string|array
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

        if ($editMode === true) {
            $options = $this->areaManager->getAreaBlockConfiguration($info->getId(), $document instanceof Snippet, true);
        }

        foreach ($columns as $column) {

            $areaBlockDataResponse = null;
            $columnName = sprintf('c%s', $column['name']);

            ob_start();

            echo $this->processEditable($document, 'areablock', $columnName, $options, $editMode, true, false);

            if ($editMode === false) {
                $areaBlockDataResponse = $this->processEditable($document, 'areablock', $columnName, $options, false);
            }

            $areaBlockHtmlResponse = ob_get_clean();

            $data[$columnName] = $editMode
                ? sprintf('<div class="%s"><div class="%s">%s</div></div>', $column['columnClass'], $column['innerClass'], $areaBlockHtmlResponse)
                : $areaBlockDataResponse;
        }

        return $editMode ? implode(PHP_EOL, $data) : $data;
    }

    private function buildAreaBlockEditable(Info $info, string $inputName, array $options, bool $editMode): string|array
    {
        $areaBlockDataResponse = '';
        $document = $info->getDocument();

        ob_start();

        echo $this->processEditable($document, 'areablock', $inputName, $options, $editMode, true, false);

        if ($editMode === false) {
            $areaBlockDataResponse = $this->processEditable($document, 'areablock', $inputName, $options, false);
        }

        $areaBlockHtmlResponse = ob_get_clean();

        return $editMode ? $areaBlockHtmlResponse : $areaBlockDataResponse;
    }
    private function buildBlockEditable(Info $info, string $inputName, array $options, array $blockElements, bool $editMode): string|array
    {
        $data = [];
        $document = $info->getDocument();

        ob_start();

        if (!array_key_exists('default', $options)) {
            $options['default'] = 1;
        }

        /** @var Block $sectionBlock */
        $blockEditable = $this->editableRenderer->getEditable($document, 'block', $inputName, $options, $editMode);

        foreach ($blockEditable->getIterator() as $blockIndex) {
            foreach ($blockElements as $blockElement) {

                $beType = $blockElement['type'];
                $beName = $blockElement['name'];
                $beConfig = $blockElement['config'];

                echo $this->processEditable($document, $beType, $beName, $beConfig, $editMode, true);

                if ($editMode === false) {
                    $data[] = $this->processEditable($document, $beType, $beName, $beConfig, false, false, true);
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
        bool $allowStandalone = false
    ) {

        $isSimple = !$this->isBlockEditable($type);
        $editable = $this->editableRenderer->getEditable($document, $type, $name, $config, $editMode);

        if ($isSimple === true && $allowStandalone === true) {

            if ($editMode === false) {

                $simpleHeadlessResponse = new HeadlessResponse('simple');
                $simpleHeadlessResponse->addAdditionalConfigData('value', $editable->getData());

                $this->getEditableWorker()->dispatch(
                    $simpleHeadlessResponse,
                    $editable->getType(),
                    $editable->getType(),
                    $editable->getName(),
                    true
                );

                return $editable->getData();
            }

            return $editable->render();
        }

        if ($forceRendering === false && $editMode === false) {
            return $editable->getData();
        }

        // yes.
        if ($isSimple === true) {
            return $editable->render();
        }

        // yes.
        echo $editable->render();

        return '';
    }

    private function isBlockEditable(string $type): bool
    {
        return in_array($type, ['area', 'block', 'areablock', 'scheduledblock'], true);
    }

    private function getEditableWorker(): EditableWorker
    {
        return \Pimcore::getContainer()->get(EditableWorker::class);
    }
}
