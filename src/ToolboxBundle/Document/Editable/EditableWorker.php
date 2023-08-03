<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable;

use Pimcore\Document\Editable\Block\BlockState;
use Pimcore\Document\Editable\Block\BlockStateStack;
use Pimcore\Extension\Document\Areabrick\AreabrickInterface;
use Pimcore\Model\Document\Editable;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;
use ToolboxBundle\Event\HeadlessElementEvent;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Registry\NormalizerRegistryInterface;
use ToolboxBundle\ToolboxEvents;

class EditableWorker
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected NormalizerRegistryInterface $normalizerRegistry,
        protected EventDispatcherInterface $eventDispatcher,
        protected BlockStateStack $blockStateStack
    ) {
    }

    public function processBrick(HeadlessResponse $data, AreabrickInterface $areabrick): void
    {
        $this->dispatch([
            'elementType'      => $data->getType(),
            'elementSubType'   => $areabrick->getId(),
            'elementNamespace' => $this->buildBrickNamespace(),
            'data'             => $this->processBrickData($data, $areabrick->getId())
        ]);
    }

    public function processEditable(HeadlessResponse $data, Editable $editable): void
    {
        $this->dispatch([
            'elementType'      => $data->getType(),
            'elementSubType'   => $editable->getType(),
            'elementNamespace' => $this->buildEditableNamespace($editable),
            'data'             => $this->processEditableData($data)
        ]);
    }

    private function dispatch(array $arguments): void
    {
        $this->eventDispatcher->dispatch(
            new HeadlessElementEvent(...$arguments),
            ToolboxEvents::HEADLESS_ELEMENT_STACK_ADD
        );
    }

    private function buildEditableNamespace(Editable $editable): string
    {
        return str_replace('.', ':', $editable->getName());
    }

    private function buildBrickNamespace(): string
    {
        $indexes = $this->getBlockState()->getIndexes();
        $blocks = $this->getBlockState()->getBlocks();

        $parts = [];
        for ($i = 0, $iMax = count($blocks); $i < $iMax; $i++) {
            $part = $blocks[$i]->getRealName();
            if (isset($indexes[$i])) {
                $part = sprintf('%s:%d', $part, $indexes[$i]);
            }

            $parts[] = $part;
        }

        return implode(':', $parts);
    }

    private function processBrickData(HeadlessResponse $data, string $areaName): array
    {
        return [
            'configuration' => $data->getBrickConfiguration(),
            'data'          => $this->processElementData($data, $areaName)
        ];
    }

    private function processEditableData(HeadlessResponse $data): array
    {
        $parentAreaName = $data->getParent();

        return [
            'data' => $parentAreaName === null ? $data->getInlineConfigElementData() : $this->processElementData($data, $parentAreaName)
        ];
    }

    private function processElementData(HeadlessResponse $data, string $areaName): array
    {
        $normalizedData = [];
        $brickConfig = $this->configManager->getAreaConfig($areaName);

        $configBlocks = [
            'config_elements'                => $data->getConfigElementData(),
            'inline_config_elements'         => $data->getInlineConfigElementData(),
            'additional_property_normalizer' => $data->getAdditionalConfigData(),
        ];

        foreach ($configBlocks as $configBlockName => $configBlockData) {

            $configElements = $brickConfig[$configBlockName] ?? [];
            foreach ($configBlockData as $configName => $configData) {

                if ($configBlockName === 'additional_property_normalizer' && array_key_exists($configName, $configElements)) {
                    $configData = $this->applyNormalizer($configElements[$configName], $configData);
                } elseif ($configBlockName !== 'additional_property_normalizer') {
                    $configNode = $this->findBrickConfigNode($configName, $configElements);
                    if ($configNode !== null && $configNode['property_normalizer'] !== null) {
                        $configData = $this->applyNormalizer($configNode['property_normalizer'], $configData);
                    }
                }

                // not normalized, use default editable data
                if ($configData instanceof Editable\EditableInterface) {
                    $configData = $configData->getData();
                }

                $normalizedData[$configName] = $configData;
            }
        }

        return $normalizedData;
    }

    private function findBrickConfigNode(string $configName, array $configElements)
    {
        if (array_key_exists($configName, $configElements)) {
            return $configElements[$configName];
        }

        foreach ($configElements as $configElement) {
            if (array_key_exists('children', $configElement) && null !== $childNode = $this->findBrickConfigNode($configName, $configElement['children'])) {
                return $childNode;
            }
        }

        return null;
    }

    private function applyNormalizer(string $normalizerName, mixed $value)
    {
        return $this->normalizerRegistry->get($normalizerName)->normalize($value, $this->configManager->getContextIdentifier());
    }

    private function getBlockState(): BlockState
    {
        return $this->blockStateStack->getCurrentState();
    }
}
