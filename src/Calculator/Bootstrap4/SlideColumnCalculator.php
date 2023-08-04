<?php

namespace ToolboxBundle\Calculator\Bootstrap4;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    public function calculateSlideColumnClasses(int $columnType, array $columnConfiguration): string
    {
        $systemClasses = [
            2 => 'col-12 col-sm-6',
            3 => 'col-12 col-sm-4',
            4 => 'col-12 col-sm-3',
            6 => 'col-12 col-sm-2',
        ];

        if (empty($columnConfiguration)) {
            return $systemClasses[$columnType] ?? 'col-12';
        }

        if (!isset($columnConfiguration['column_classes']) || !isset($columnConfiguration['column_classes'][$columnType])) {
            return $systemClasses[$columnType] ?? 'col-12';
        }

        return $columnConfiguration['column_classes'][$columnType];
    }
}
