<?php

namespace ToolboxBundle\Calculator;

interface SlideColumnCalculatorInterface
{
    public function calculateSlideColumnClasses(int $columnType, array $columnConfiguration): string;
}
