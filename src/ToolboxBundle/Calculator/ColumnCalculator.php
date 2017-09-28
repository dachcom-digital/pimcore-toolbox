<?php

namespace ToolboxBundle\Calculator;

class ColumnCalculator implements ColumnCalculatorInterface
{
    public function calculateColumns($value, $columnConfiguration = [])
    {
        throw new \Exception('please define a valid column calculator in toolbox.theme.calculator configuration tree');
    }
}