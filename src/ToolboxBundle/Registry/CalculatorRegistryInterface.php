<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

interface CalculatorRegistryInterface
{
    /**
     * @param $id
     * @param $service
     * @param $type
     * @return void
     */
    public function register($id, $service, $type);

    /**
     * @param $alias
     * @return SlideColumnCalculatorInterface
     */
    public function getSlideColumnCalculator($alias);

    /**
     * @param $alias
     * @return ColumnCalculatorInterface
     */
    public function getColumnCalculator($alias);

    /**
     * @param $alias
     * @param $type
     * @return bool
     */
    public function has($alias, $type);

    /**
     * @param $alias
     * @param $type
     * @throws \Exception
     * @return SlideColumnCalculatorInterface|ColumnCalculatorInterface
     */
    public function get($alias, $type);
}