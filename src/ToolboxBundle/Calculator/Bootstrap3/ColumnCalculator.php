<?php

namespace ToolboxBundle\Calculator\Bootstrap3;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    /**
     * @param       $value
     * @param array $columnConfiguration
     *
     * @return array
     */
    public function calculateColumns($value, $columnConfiguration = [])
    {
        $columns = [];

        if (empty($value)) {
            return $columns;
        }

        $t = explode('_', $value);

        //remove "column" in string.
        $_columns = array_splice($t, 1);

        $bootstrapOffsetConfig = [];

        foreach ($_columns as $i => $columnClass) {

            $bootstrapClassConfig = [
                'xs' => 'col-xs-12',
                'sm' => 'col-sm-' . $columnClass,
                'md' => 'col-md-' . $columnClass
            ];

            //config is an array, use special breakpoint classes!
            if (isset($columnConfiguration[$value]['breakpoints'])) {

                $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                    $customBreakPointDataColumns = explode('_', $customBreakPointData);
                    $customColAmount = isset($customBreakPointDataColumns[$i]) ? $customBreakPointDataColumns[$i] : 12;
                    $bootstrapClassConfig[$customBreakPointName] = 'col-' . $customBreakPointName . '-' . $customColAmount;
                }
            }

            if (substr($columnClass, 0, 1) === 'o') {

                $offset = (int)substr($columnClass, 1);

                $bootstrapOffsetConfig = [
                    'sm' => 'col-sm-offset-' . $offset,
                    'md' => 'col-md-offset-' . $offset
                ];

                //config is an array, use special breakpoint classes!
                if (isset($columnConfiguration[$value]['breakpoints'])) {

                    $customBreakPoints = $columnConfiguration[$value]['breakpoints'];
                    foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                        $customBreakPointDataColumns = explode('_', $customBreakPointData);
                        $customColAmount = isset($customBreakPointDataColumns[$i]) ? $customBreakPointDataColumns[$i] : 12;

                        if (substr($customColAmount, 0, 1) === 'o') {
                            $customOffset = (int)substr($customColAmount, 1);
                            $bootstrapOffsetConfig[$customBreakPointName] = 'col-' . $customBreakPointName . '-offset-' . $customOffset;
                        }
                    }
                }

                //skip column because of offset column.
                continue;
            }

            $columns[] = [
                'columnClass' => join(' ', $bootstrapClassConfig) . ' ' . join(' ', $bootstrapOffsetConfig),
                'columnType'  => $value,
                'name'        => 'column_' . $i
            ];

            $bootstrapOffsetConfig = [];
        }

        return $columns;
    }
}
