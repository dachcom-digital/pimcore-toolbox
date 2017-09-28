<?php

namespace ToolboxBundle\Calculator;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    public function calculateSlideColumnClasses($columnType, $columnConfiguration)
    {
        throw new \Exception('please define a valid slide column calculator in toolbox.theme.calculator configuration tree');

    }
}