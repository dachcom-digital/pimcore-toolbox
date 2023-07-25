<?php

namespace ToolboxBundle\Calculator\Bootstrap4;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    protected ConfigManagerInterface $configManager;

    public function setConfigManager(ConfigManagerInterface $configManager): self
    {
        $this->configManager = $configManager;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function calculateColumns(?string $value, ?array $customColumnConfiguration = null): array
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
            $columnConfiguration = $columnConfigNode['config']['store'] ?? [];
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
                'xs' => 'col-' . $gridSize,
                'sm' => 'col-sm-' . $columnClass
            ];

            //config is an array, use special breakpoint classes!
            if (isset($columnConfiguration[$value]['breakpoints'])) {
                $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                    $customBreakPointDataColumns = explode('_', $customBreakPointData);
                    $customColAmount = isset($customBreakPointDataColumns[$i]) ? (int) $customBreakPointDataColumns[$i] : $gridSize;
                    $bpPrefix = $customBreakPointName === 'xs' ? '' : $customBreakPointName . '-';
                    $bootstrapClassConfig[$customBreakPointName] = 'col-' . $bpPrefix . $customColAmount;
                    $gridConfig[$customBreakPointName] = $customColAmount;
                }
            }

            if (str_starts_with($columnClass, 'o')) {
                $offset = (int) substr($columnClass, 1);

                $gridOffsetConfig = $customColumnConfiguration ? [] : [
                    'sm' => $offset
                ];

                $bootstrapOffsetConfig = $customColumnConfiguration ? [] : [
                    'sm' => 'offset-sm-' . $offset
                ];

                //config is an array, use special breakpoint classes!
                if (isset($columnConfiguration[$value]['breakpoints'])) {
                    $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                    foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                        $customBreakPointDataColumns = explode('_', $customBreakPointData);
                        $customColAmount = isset($customBreakPointDataColumns[$i]) ? $customBreakPointDataColumns[$i] : $gridSize;
                         if (str_starts_with($customColAmount, 'o')) {
                            $customOffset = (int) substr($customColAmount, 1);
                            $bpPrefix = $customBreakPointName === 'xs' ? '' : $customBreakPointName . '-';
                            $bootstrapOffsetConfig[$customBreakPointName] = 'offset-' . $bpPrefix . $customOffset;
                            $gridOffsetConfig[$customBreakPointName] = $customOffset;
                        }
                    }
                }

                //skip column because of offset column.
                continue;
            }

            $columnName = $strictColumnCounter ? 'column_' . $i : 'column_' . $columnCounter;
            $columns[] = [
                'columnClass' => implode(' ', $bootstrapClassConfig) . ' ' . join(' ', $bootstrapOffsetConfig),
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

    public function getColumnInfoForAdjuster(?string $value, ?array $customColumnConfiguration = null): bool|array
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
