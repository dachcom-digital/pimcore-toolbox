<?php

namespace ToolboxBundle\Calculator;

interface ColumnCalculatorInterface {

    /**
     * @param       $value
     * @param array $columnConfiguration
     * @param int $gridSize
     *
     * @return array
     */
    public function calculateColumns($value, $columnConfiguration = [], $gridSize = 12);

    /**
     * @param string $currentColumn
     * @param array  $columnConfiguration
     * @param array  $gridSettings
     * @return mixed
     */
    public function getColumnInfoForAdjuster($currentColumn = '', $columnConfiguration = [], $gridSettings = []);
}