<?php

namespace ToolboxBundle\Calculator\Bootstrap3;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    protected ?ConfigManagerInterface $configManager = null;

    public function setConfigManager(ConfigManagerInterface $configManager): static
    {
        $this->configManager = $configManager;
        $this->configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL);

        return $this;
    }

    public function calculateColumns(string $value, ?array $customColumnConfiguration = null): array
    {
        $themeSettings = $this->configManager->getConfig('theme');
        $gridSettings = $themeSettings['grid'];
        $gridSize = $gridSettings['grid_size'];

        $flags = $this->configManager->getConfig('flags');
        $strictColumnCounter = $flags['strict_column_counter'];

        if ($customColumnConfiguration !== null) {
            $columnConfiguration = $customColumnConfiguration;
        } else {
            $columnConfigNode = $this->configManager->getAreaElementConfig('columns', 'type');
            $columnConfiguration = isset($columnConfigNode['config']['store']) ? $columnConfigNode['config']['store'] : [];
        }

        $columns = [];
        if (empty($value)) {
            return $columns;
        }

        $t = explode('_', $value);

        //remove "column" in string.
        $_columns = array_splice($t, 1);

        $bootstrapOffsetConfig = [];
        $gridOffsetConfig = [];

        $columnCounter = 0;
        foreach ($_columns as $i => $columnClass) {
            $gridConfig = $customColumnConfiguration ? [] : [
                'xs' => $gridSize,
                'sm' => (int) $columnClass
            ];

            $bootstrapClassConfig = $customColumnConfiguration ? [] : [
                'xs' => 'col-xs-' . $gridSize,
                'sm' => 'col-sm-' . $columnClass
            ];

            //config is an array, use special breakpoint classes!
            if (isset($columnConfiguration[$value]['breakpoints'])) {
                $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                    $customBreakPointDataColumns = explode('_', $customBreakPointData);
                    $customColAmount = isset($customBreakPointDataColumns[$i]) ? (int) $customBreakPointDataColumns[$i] : $gridSize;
                    $bootstrapClassConfig[$customBreakPointName] = 'col-' . $customBreakPointName . '-' . $customColAmount;
                    $gridConfig[$customBreakPointName] = $customColAmount;
                }
            }

            if (substr($columnClass, 0, 1) === 'o') {
                $offset = (int) substr($columnClass, 1);

                $gridOffsetConfig = $customColumnConfiguration ? [] : [
                    'sm' => $offset
                ];

                $bootstrapOffsetConfig = $customColumnConfiguration ? [] : [
                    'sm' => 'col-sm-offset-' . $offset
                ];

                //config is an array, use special breakpoint classes!
                if (isset($columnConfiguration[$value]['breakpoints'])) {
                    $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                    foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                        $customBreakPointDataColumns = explode('_', $customBreakPointData);
                        $customColAmount = isset($customBreakPointDataColumns[$i]) ? $customBreakPointDataColumns[$i] : $gridSize;
                        if (substr($customColAmount, 0, 1) === 'o') {
                            $customOffset = (int) substr($customColAmount, 1);
                            $bootstrapOffsetConfig[$customBreakPointName] = 'col-' . $customBreakPointName . '-offset-' . $customOffset;
                            $gridOffsetConfig[$customBreakPointName] = $customOffset;
                        }
                    }
                }

                //skip column because of offset column.
                continue;
            }

            $columnName = $strictColumnCounter ? 'column_' . $i : 'column_' . $columnCounter;
            $columns[] = [
                'columnClass' => join(' ', $bootstrapClassConfig) . ' ' . join(' ', $bootstrapOffsetConfig),
                'columnData'  => [
                    'grid'       => $gridConfig,
                    'gridOffset' => $gridOffsetConfig
                ],
                'columnType'  => $value,
                'name'        => $columnName
            ];

            $columnCounter++;
            $bootstrapOffsetConfig = [];
            $gridOffsetConfig = [];
        }

        return $columns;
    }

    public function getColumnInfoForAdjuster(string $value, ?array $customColumnConfiguration = null)
    {
        $columnData = $this->calculateColumns($value, $customColumnConfiguration);

        $themeSettings = $this->configManager->getConfig('theme');
        $gridSettings = $themeSettings['grid'];

        //no data found, calculateColumns does not return columnData!
        if (count($columnData) === 0 || !isset($columnData[0]['columnData'])) {
            return false;
        }

        $breakPoints = $gridSettings['breakpoints'];
        foreach ($gridSettings['breakpoints'] as $index => $breakpoint) {
            $breakpointColumnData = [];
            foreach ($columnData as $column) {
                $columnConfig = ['amount' => $gridSettings['grid_size']];

                //offset
                if (array_key_exists($breakpoint['identifier'], $column['columnData']['gridOffset'])) {
                    $columnConfig['offset'] = $column['columnData']['gridOffset'][$breakpoint['identifier']];
                } else {
                    $columnConfig['offset'] = null;
                }

                //grid size
                if (array_key_exists($breakpoint['identifier'], $column['columnData']['grid'])) {
                    $columnConfig['value'] = $column['columnData']['grid'][$breakpoint['identifier']];
                } else {
                    $columnConfig['value'] = null;
                }

                $breakpointColumnData[] = $columnConfig;
            }
            $breakPoints[$index]['grid'] = $breakpointColumnData;
        }

        return $breakPoints;
    }
}
