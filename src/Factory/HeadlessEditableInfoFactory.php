<?php

declare(strict_types=1);

namespace ToolboxBundle\Factory;

use Pimcore\Model\Document;
use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Document\Editable\DTO\HeadlessEditableInfo;
use ToolboxBundle\Manager\AreaManagerInterface;

class HeadlessEditableInfoFactory
{
    public function __construct(protected AreaManagerInterface $areaManager)
    {
    }

    public function createViaBrick(Document\Editable\Area\Info $info, bool $editMode, array $item): HeadlessEditableInfo
    {
        return new HeadlessEditableInfo(
            document: $info->getDocument(),
            editableId: $info->getId(),
            name: $item['name'],
            type: $item['type'],
            label: $item['label'] ?? null,
            brickParent: null,
            editableConfiguration: null,
            config: $this->createConfig($info->getDocument(), $item, $editMode, $item['type'] === 'areablock' ? $info->getId() : null),
            params: $info->getParams(),
            children: $this->createChildren($item, $info->getDocument(), $info->getId(), $editMode, $info->getParams(), true),
            editMode: $editMode
        );
    }

    public function createViaEditable(Document $document, mixed $editableId, bool $editMode, array $item, array $parameters = []): HeadlessEditableInfo
    {
        return new HeadlessEditableInfo(
            document: $document,
            editableId: $editableId,
            name: $item['name'],
            type: $item['type'],
            label: $item['label'] ?? null,
            brickParent: null,
            editableConfiguration: $this->createEditableConfiguration($item),
            config: $this->createConfig($document, $item, $editMode, $item['type'] === 'areablock' ? $item['name'] : null),
            params: $parameters,
            children: $this->createChildren($item, $document, $editableId, $editMode, $parameters),
            editMode: $editMode,
            standAloneAware: true
        );
    }

    protected function createChildren(
        array $item,
        Document $document,
        mixed $editableId,
        bool $editMode,
        array $parameters = [],
        bool $hasBrickParent = false
    ): array {

        if ($item['type'] === 'column') {
            return $this->createColumnChildren($item, $document, $editableId, $editMode, $parameters);
        }

        if ($item['type'] === 'block') {
            return $this->createBlockChildren($item, $document, $editableId, $editMode, $parameters, $hasBrickParent);
        }

        return [];
    }

    protected function createColumnChildren(
        array $item,
        Document $document,
        mixed $editableId,
        bool $editMode,
        array $parameters = []
    ): array {

        $resolvedChildren = [];
        $columns = $parameters['columns'] ?? [];

        foreach ($columns as $columnData) {
            $columnName = sprintf('c%s', $columnData['name']);
            $resolvedChildren[] = new HeadlessEditableInfo(
                document: $document,
                editableId: $editableId,
                name: $columnName,
                type: 'areablock',
                label: null,
                brickParent: $editableId,
                editableConfiguration: null,
                config: $this->createConfig($document, $item, $editMode, $editableId),
                params: array_merge($parameters, $columnData),
                children: [],
                editMode: $editMode
            );
        }

        return $resolvedChildren;
    }

    protected function createBlockChildren(
        array $item,
        Document $document,
        mixed $editableId,
        bool $editMode,
        array $parameters = [],
        bool $hasBrickParent = false
    ): array {

        $resolvedChildren = [];
        $children = $item['children'] ?? [];

        foreach ($children as $childName => $childData) {

            $brickParent = null;
            $editableConfiguration = null;

            if ($hasBrickParent === true) {
                $brickParent = $editableId;
            } else {
                $editableConfiguration = $this->createEditableConfiguration($childData);
            }

            $resolvedChildren[] = new HeadlessEditableInfo(
                document: $document,
                editableId: $editableId,
                name: array_key_exists('name', $childData) ? $childData['name'] : $childName,
                type: $childData['type'],
                label: $childData['label'] ?? null,
                brickParent: $brickParent,
                editableConfiguration: $editableConfiguration,
                config: $this->createConfig($document, $childData, $editMode, null),
                params: $parameters,
                children: [],
                editMode: $editMode,
                standAloneAware: true,
            );
        }

        return $resolvedChildren;
    }

    protected function createConfig(Document $document, array $item, bool $editMode, ?string $areaBlockName = null): array
    {
        $config = $item['config'] ?? [];

        if ($item['type'] === 'block' && !array_key_exists('default', $config)) {
            $config['default'] = 1;
        }

        // override config with area block config
        if ($areaBlockName !== null && $editMode === true) {
            $config = $this->areaManager->getAreaBlockConfiguration($areaBlockName, $document instanceof Snippet, true);
        }

        return $config;
    }

    protected function createEditableConfiguration(array $item): array
    {
        return array_filter($item, static function ($key) {
            return !in_array($key, ['config', 'name', 'type', 'children'], true);
        }, ARRAY_FILTER_USE_KEY);
    }
}
