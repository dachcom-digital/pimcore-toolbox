<?php

namespace ToolboxBundle\Calculator\UIkit3;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    protected ConfigManagerInterface $configManager;

    public function setConfigManager(ConfigManagerInterface $configManager): self
    {
        $this->configManager = $configManager;
        $this->configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL);

        return $this;
    }

    public function calculateColumns(?string $value, ?array $customColumnConfiguration = null): array
    {
        $themeSettings = $this->configManager->getConfig('theme');
        $gridSettings = $themeSettings['grid'];
        $gridSize = $gridSettings['grid_size'];

        $flags = $this->configManager->getConfig('flags');
        $strictColumnCounter = $flags['strict_column_counter'];

        if ($customColumnConfiguration !== null) {
            // Custom Settings
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

        // remove "column" in string.
        $_columns = array_splice($t, 1);
        $columnCounter = 0;

        foreach ($_columns as $i => $columnClass) {
            // set when no custom config exists
            $gridConfig = $customColumnConfiguration ? [] : [
                's' => $gridSize,
                'm' => (int) $columnClass
            ];

            $ukCol = $this->getUIKitCol($columnClass);

            $ukClassConfig = $customColumnConfiguration ? [] : [
                's' => 'uk-width-1-1',
                'm' => 'uk-width-' . $ukCol . '@s'
            ];

            // config is an array, use special breakpoint classes!
            if (isset($columnConfiguration[$value]['breakpoints'])) {
                $customBreakPoints = $columnConfiguration[$value]['breakpoints'];

                foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                    $customBreakPointDataColumns = explode('_', $customBreakPointData);
                    $customColAmount = $customBreakPointDataColumns[$i] ?? 11;
                    $ukCol = $this->getUIKitCol($customColAmount);
                    $ukClassConfig[$customBreakPointName] = 'uk-width-' . $ukCol . '@' . $customBreakPointName;
                    $gridConfig[$customBreakPointName] = $customColAmount;
                }

                // smallest one without breakpoint modifier
                if (array_key_exists('xs', $customBreakPoints)) {
                    $customBreakPointData = $customBreakPoints['xs'];
                    $customBreakPointDataColumns = explode('_', $customBreakPointData);
                    $customColAmount = $customBreakPointDataColumns[$i] ?? 11;
                    $ukCol = $this->getUIKitCol($customColAmount);
                    $ukClassConfig['xs'] = 'uk-width-' . $ukCol;
                }
            }

            $columnName = $strictColumnCounter ? 'column_' . $i : 'column_' . $columnCounter;
            $columns[] = [
                'columnClass' => implode(' ', $ukClassConfig),
                'columnData'  => [
                    'grid'       => $gridConfig,
                    'gridOffset' => []
                ],
                'columnType'  => $value,
                'name'        => $columnName
            ];
            
            $columnCounter++;
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
                //  Offset
                $columnConfig['offset'] = null;

                // Grid size
                if (array_key_exists($breakpoint['identifier'], $column['columnData']['grid'])) {
                    if ($column['columnData'] === 1) {
                        $columnConfig['value'] = '1 Test';
                    } else {
                        $columnConfig['value'] = $column['columnData']['grid'][$breakpoint['identifier']];
                    }
                } else {
                    $columnConfig['value'] = null;
                }
                $breakpointColumnData[] = $columnConfig;
            }
            $breakPoints[$index]['grid'] = $breakpointColumnData;
        }

        return $breakPoints;
    }

    private function getUIKitCol(mixed $numerator): string
    {
        if (is_numeric($numerator)) {
            $split = str_split($numerator);

            return $split[0] . '-' . $split[1];
        }

        return $numerator;
    }
}
