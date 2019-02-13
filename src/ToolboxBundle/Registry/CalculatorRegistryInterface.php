<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

interface CalculatorRegistryInterface
{
    /**
     * @param string $id
     * @param string $service
     * @param string $type
     */
    public function register($id, $service, $type);

    /**
     * @param string $alias
     *
     * @return SlideColumnCalculatorInterface
     */
    public function getSlideColumnCalculator($alias);

    /**
     * @param string $alias
     *
     * @return ColumnCalculatorInterface
     */
    public function getColumnCalculator($alias);

    /**
     * @param string $alias
     * @param string $type
     *
     * @return bool
     */
    public function has($alias, $type);

    /**
     * @param string $alias
     * @param string $type
     *
     * @throws \Exception
     *
     * @return SlideColumnCalculatorInterface|ColumnCalculatorInterface
     */
    public function get($alias, $type);
}
