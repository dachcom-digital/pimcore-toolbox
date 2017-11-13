<?php

namespace ToolboxBundle\Calculator\Bootstrap4;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    /**
     * @param              $value
     * @param string|array $columnConfiguration
     * @param int          $gridSize
     *
     * @return array
     */
    public function calculateColumns($value, $columnConfiguration = [], $gridSize = 12)
    {
        $columns = [];

        if (empty($value)) {
            return $columns;
        }

        $t = explode('_', $value);

        //remove "column" in string.
        $_columns = array_splice($t, 1);

        $bootstrapOffsetConfig = [];
        $gridOffsetConfig = [];

        foreach ($_columns as $i => $columnClass) {

            $gridConfig = [
                'xs' => $gridSize,
                'sm' => (int)$columnClass
            ];

            $bootstrapClassConfig = [
                'xs' => 'col-' . $gridSize,
                'sm' => 'col-sm-' . $columnClass
            ];

            //config is an array, use special breakpoint classes!
            if (isset($columnConfiguration[$value]['breakpoints'])) {
                $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                    $customBreakPointDataColumns = explode('_', $customBreakPointData);
                    $customColAmount = isset($customBreakPointDataColumns[$i]) ? (int)$customBreakPointDataColumns[$i] : $gridSize;
                    $bpPrefix = $customBreakPointName === 'xs' ? '' : $customBreakPointName . '-';
                    $bootstrapClassConfig[$customBreakPointName] = 'col-' . $bpPrefix . $customColAmount;
                    $gridConfig[$customBreakPointName] = $customColAmount;
                }
            }

            if (substr($columnClass, 0, 1) === 'o') {

                $offset = (int)substr($columnClass, 1);

                $gridOffsetConfig = [
                    'sm' => $offset
                ];

                $bootstrapOffsetConfig = [
                    'sm' => 'offset-sm-' . $offset
                ];

                //config is an array, use special breakpoint classes!
                if (isset($columnConfiguration[$value]['breakpoints'])) {
                    $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                    foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                        $customBreakPointDataColumns = explode('_', $customBreakPointData);
                        $customColAmount = isset($customBreakPointDataColumns[$i]) ? (int)$customBreakPointDataColumns[$i] : $gridSize;
                        if (substr($customColAmount, 0, 1) === 'o') {
                            $customOffset = (int)substr($customColAmount, 1);
                            $bpPrefix = $customBreakPointName === 'xs' ? '' : $customBreakPointName . '-';
                            $bootstrapOffsetConfig[$customBreakPointName] = 'offset-' . $bpPrefix . $customOffset;
                            $gridOffsetConfig[$customBreakPointName] = $customOffset;
                        }
                    }
                }

                //skip column because of offset column.
                continue;
            }

            $columns[] = [
                'columnClass' => join(' ', $bootstrapClassConfig) . ' ' . join(' ', $bootstrapOffsetConfig),
                'columnData'  => [
                    'grid'       => $gridConfig,
                    'gridOffset' => $gridOffsetConfig
                ],
                'columnType'  => $value,
                'name'        => 'column_' . $i
            ];

            $bootstrapOffsetConfig = [];
            $gridOffsetConfig = [];
        }

        return $columns;
    }

    /**
     * @param string       $currentColumn
     * @param string|array $columnConfiguration
     * @param array        $gridSettings
     * @return mixed
     */
    public function getColumnInfoForAdjuster($currentColumn = '', $columnConfiguration = [], $gridSettings = [])
    {
        $columnData = $this->calculateColumns($currentColumn, $columnConfiguration, $gridSettings['grid_size']);

        //no data found o calculateColumns does not return columnData!
        if (count($columnData) === 0 || !isset($columnData[0]['columnData'])) {
            return FALSE;
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
                    $columnConfig['offset'] = NULL;
                }

                //grid size
                if (array_key_exists($breakpoint['identifier'], $column['columnData']['grid'])) {
                    $columnConfig['value'] = $column['columnData']['grid'][$breakpoint['identifier']];
                } else {
                    $columnConfig['value'] = NULL;
                }

                $breakpointColumnData[] = $columnConfig;

            }
            $breakPoints[$index]['grid'] = $breakpointColumnData;
        }

        return $breakPoints;
    }
}
