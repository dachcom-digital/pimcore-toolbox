<?php

namespace ToolboxBundle\Calculator;

interface ColumnCalculatorInterface {

    /**
     * @param       $value
     * @param array $columnConfiguration
     *
     * @return array
     */
    public function calculateColumns($value, $columnConfiguration = []);
}