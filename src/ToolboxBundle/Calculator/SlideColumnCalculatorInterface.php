<?php

namespace ToolboxBundle\Calculator;

interface SlideColumnCalculatorInterface
{
    /**
     * @param int   $columnType
     * @param array $columnConfiguration
     *
     * @return string
     */
    public function calculateSlideColumnClasses($columnType, $columnConfiguration);
}