<?php

namespace ToolboxBundle\Calculator\Bootstrap3;

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
            2 => 'col-xs-12 col-sm-6',
            3 => 'col-xs-12 col-sm-4',
            4 => 'col-xs-12 col-sm-3',
            6 => 'col-xs-12 col-sm-2',
        ];

        if (empty($columnConfiguration)) {
            return isset($systemClasses[$columnType]) ? $systemClasses[$columnType] : 'col-xs-12';
        }

        if (!isset($columnConfiguration['column_classes']) || !isset($columnConfiguration['column_classes'][$columnType])) {
            return isset($systemClasses[$columnType]) ? $systemClasses[$columnType] : 'col-xs-12';
        }

        return $columnConfiguration['column_classes'][$columnType];
    }
}
