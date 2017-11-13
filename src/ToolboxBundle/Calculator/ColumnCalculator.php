<?php

namespace ToolboxBundle\Calculator;

class ColumnCalculator implements ColumnCalculatorInterface
{
    public function calculateColumns($value, $columnConfiguration = [], $gridSize = 12)
    {
        throw new \Exception('please define a valid column calculator in toolbox.theme.calculator configuration tree');
    }

    public function getColumnInfoForAdjuster($currentColumn = '', $columnConfiguration = [], $gridSettings = [])
    {
        throw new \Exception('please define a valid column calculator in toolbox.theme.calculator configuration tree');
    }
}