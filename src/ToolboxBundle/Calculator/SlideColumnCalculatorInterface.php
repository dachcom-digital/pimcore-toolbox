<?php

namespace ToolboxBundle\Calculator;

interface SlideColumnCalculatorInterface {

    /**
     * @param $columnType
     * @param $columnConfiguration
     *
     * @return mixed
     */
    public function calculateSlideColumnClasses($columnType, $columnConfiguration);
}