<?php

namespace ToolboxBundle\Calculator\Bootstrap4;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    /**
     * @param int   $columnType
     * @param array $columnConfiguration
     *
     * @return string
     */
    public function calculateSlideColumnClasses($columnType, $columnConfiguration)
    {
        $columnType = (int) $columnType;

        $systemClasses = [
            2 => 'col-12 col-sm-6',
            3 => 'col-12 col-sm-4',
            4 => 'col-12 col-sm-3',
            6 => 'col-12 col-sm-2',
        ];

        if (empty($columnConfiguration)) {
            return isset($systemClasses[$columnType]) ? $systemClasses[$columnType] : 'col-12';
        }

        if (!isset($columnConfiguration['column_classes']) || !isset($columnConfiguration['column_classes'][$columnType])) {
            return isset($systemClasses[$columnType]) ? $systemClasses[$columnType] : 'col-12';
        }

        return $columnConfiguration['column_classes'][$columnType];
    }
}
