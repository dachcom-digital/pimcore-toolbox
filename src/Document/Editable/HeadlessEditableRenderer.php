<?php

namespace ToolboxBundle\Document\Editable;

use Pimcore\Extension\Document\Areabrick\Exception\ConfigurationException;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Block;
use Pimcore\Templating\Renderer\EditableRenderer;
use ToolboxBundle\Document\Editable\DTO\HeadlessEditableInfo;
use ToolboxBundle\Document\Response\HeadlessResponse;
use Twig\Environment;

class HeadlessEditableRenderer
{
    public function __construct(
        protected Environment $templating,
        protected EditableRenderer $editableRenderer,
        protected EditableWorker $editableWorker
    ) {
    }

    public function renderBrickWithWrapper(array $contentBlocks): string
    {
        $brickHash = $this->editableWorker->buildBrickHash();

        return sprintf(
            '<div class="inline-config-area" data-headless-element-hash="%s">%s</div>',
            $brickHash,
            implode(PHP_EOL, $contentBlocks)
        );
    }

    public function renderStandaloneEditableWithWrapper(string $contentBlock, Editable $editable): string
    {
        $editableHash = $this->editableWorker->buildEditableHash($editable);

        return sprintf(
            '<div class="inline-config-area" data-headless-element-hash="%s">%s</div>',
            $editableHash,
            $contentBlock
        );
    }

    public function renderEditableWithWrapper(string $type, array $viewParameters): string
    {
        $resolvedTemplate = $this->templating->resolveTemplate([
            sprintf('@Toolbox/admin/headless/editable_%s.html.twig', $type),
            '@Toolbox/admin/headless/editable.html.twig'
        ]);

        return $this->templating->render($resolvedTemplate, $viewParameters);
    }

    public function buildEditable(HeadlessEditableInfo $headlessEditableInfo): Editable|string|array
    {
        return match ($headlessEditableInfo->getType()) {
            'block' => $this->buildBlockEditable($headlessEditableInfo),
            'area' => $this->buildAreaEditable($headlessEditableInfo),
            'areablock' => $this->buildAreaBlockEditable($headlessEditableInfo),
            'column' => $this->buildColumnEditable($headlessEditableInfo),
            default => $this->buildStandardEditable($headlessEditableInfo),
        };
    }

    private function buildStandardEditable(HeadlessEditableInfo $headlessEditableInfo): Editable|string|array
    {
        return $this->processEditable($headlessEditableInfo, $this->getEditable($headlessEditableInfo));
    }

    private function buildColumnEditable(HeadlessEditableInfo $headlessEditableInfo): string|array
    {
        $data = [];
        $editMode = $headlessEditableInfo->isEditMode();

        if (!$headlessEditableInfo->hasChildren()) {
            throw new ConfigurationException(
                sprintf(
                    'Cannot render "columns" editable. Brick "%s" does not provide any "columns" definitions in $info parameter',
                    $headlessEditableInfo->getId()
                )
            );
        }

        foreach ($headlessEditableInfo->getChildren() as $headlessColumnEditableInfo) {

            $areaBlockDataResponse = null;
            $editable = $this->getEditable($headlessEditableInfo);

            ob_start();

            echo $this->processEditable($headlessColumnEditableInfo, $editable, true);

            if ($editMode === false) {
                $areaBlockDataResponse = $this->processEditable($headlessColumnEditableInfo, $editable);
            }

            $areaBlockHtmlResponse = ob_get_clean();

            $data[$headlessColumnEditableInfo->getName()] = $editMode
                ? sprintf(
                    '<div class="%s"><div class="%s">%s</div></div>',
                    $headlessColumnEditableInfo->getParam('columnClass'),
                    $headlessColumnEditableInfo->getParam('innerClass'),
                    $areaBlockHtmlResponse
                )
                : $areaBlockDataResponse;
        }

        return $editMode ? implode(PHP_EOL, $data) : $data;
    }

    private function buildAreaEditable(HeadlessEditableInfo $headlessEditableInfo): string|array
    {
        $areaDataResponse = '';
        $editMode = $headlessEditableInfo->isEditMode();
        $editable = $this->getEditable($headlessEditableInfo);

        ob_start();

        echo $this->processEditable($headlessEditableInfo, $editable, true);

        if ($editMode === false) {
            $areaDataResponse = $this->processEditable($headlessEditableInfo, $editable);
        }

        $areaHtmlResponse = ob_get_clean();

        return $editMode ? $areaHtmlResponse : $areaDataResponse;
    }

    private function buildAreaBlockEditable(HeadlessEditableInfo $headlessEditableInfo): string|array
    {
        $areaBlockDataResponse = '';
        $editMode = $headlessEditableInfo->isEditMode();
        $editable = $this->getEditable($headlessEditableInfo);

        ob_start();

        echo $this->processEditable($headlessEditableInfo, $editable, true);

        if ($editMode === false) {
            $areaBlockDataResponse = $this->processEditable($headlessEditableInfo, $editable);
        }

        $areaBlockHtmlResponse = ob_get_clean();

        return $editMode ? $areaBlockHtmlResponse : $areaBlockDataResponse;
    }

    private function buildBlockEditable(HeadlessEditableInfo $headlessEditableInfo): string|array
    {
        $data = [];
        $document = $headlessEditableInfo->getDocument();
        $config = $headlessEditableInfo->getConfig();
        $editMode = $headlessEditableInfo->isEditMode();

        ob_start();

        /** @var Block $blockEditable */
        $blockEditable = $this->editableRenderer->getEditable($document, 'block', $headlessEditableInfo->getName(), $config, $headlessEditableInfo->isEditMode());

        foreach ($blockEditable->getIterator() as $blockIndex) {

            $blockHash = $this->editableWorker->buildBlockHash($headlessEditableInfo->getName(), $blockIndex);
            $blockNamespace = sprintf('%s:%s', $headlessEditableInfo->getName(), $blockIndex);

            if ($editMode === true) {
                echo sprintf('<a data-headless-element-hash="%s"></a>', $blockHash);
            }

            $this->editableWorker->processVirtualElement(HeadlessResponse::TYPE_EDITABLE, 'block', $blockHash, $blockNamespace);

            foreach ($headlessEditableInfo->getChildren() as $childHeadlessEditableInfo) {

                $editable = $this->getEditable($childHeadlessEditableInfo);

                ob_start();

                echo $this->processEditable($childHeadlessEditableInfo, $editable, true);

                $renderedBlockEditable = ob_get_clean();

                echo $this->renderEditableWithWrapper($childHeadlessEditableInfo->getType(), [
                    'item'     => [
                        'label'       => $childHeadlessEditableInfo->getLabel(),
                        'description' => null
                    ],
                    'editable' => $renderedBlockEditable
                ]);

                if ($editMode === false) {
                    $data[] = $this->processEditable($childHeadlessEditableInfo, $editable);
                }
            }
        }

        $areaBlockHtmlResponse = ob_get_clean();

        return $editMode ? $areaBlockHtmlResponse : $data;
    }

    private function processEditable(HeadlessEditableInfo $headlessEditableInfo, Editable $editable, bool $forceRendering = false): mixed
    {
        $editMode = $headlessEditableInfo->isEditMode();
        $type = $headlessEditableInfo->getType();
        $isSimple = !$headlessEditableInfo->isBlockEditable();

        if ($headlessEditableInfo->isStandAlone() === true) {

            if ($editMode === false) {

                $simpleHeadlessResponse = new HeadlessResponse(
                    HeadlessResponse::TYPE_EDITABLE,
                    $type,
                    $headlessEditableInfo->getBrickParent(),
                    $headlessEditableInfo->getEditableConfiguration(),
                );

                $simpleHeadlessResponse->setInlineConfigElementData([$editable->getRealName() => $editable]);

                $this->editableWorker->processEditable($simpleHeadlessResponse, $editable);

                return $editable;
            }

            return $editable->render();
        }

        if ($forceRendering === false && $editMode === false) {
            return $editable;
        }

        if ($isSimple === true) {
            return $editable->render();
        }

        echo $editable->render();

        return '';
    }

    public function getEditable(HeadlessEditableInfo $headlessEditableInfo): Editable
    {
        $editMode = $headlessEditableInfo->isEditMode();
        $type = $headlessEditableInfo->getType();
        $name = $headlessEditableInfo->getName();
        $config = $headlessEditableInfo->getConfig();
        $document = $headlessEditableInfo->getDocument();

        /** @var Editable $editable */
        $editable = $this->editableRenderer->getEditable($document, $type, $name, $config, $editMode);

        return $editable;
    }
}
