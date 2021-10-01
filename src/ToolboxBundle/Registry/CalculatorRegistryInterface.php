<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

interface CalculatorRegistryInterface
{
    public function register(string $id, mixed $service, string $type): void;

    public function getSlideColumnCalculator(string $alias): SlideColumnCalculatorInterface;

    public function getColumnCalculator(string $alias): ColumnCalculatorInterface;

    public function has(string $alias, string $type): bool;

    /**
     * @throws \Exception
     */
    public function get(string $alias, string $type): SlideColumnCalculatorInterface|ColumnCalculatorInterface;
}
