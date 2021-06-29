<?php

namespace ToolboxBundle\Calculator;

use ToolboxBundle\Manager\ConfigManagerInterface;

interface ColumnCalculatorInterface
{
    public function setConfigManager(ConfigManagerInterface $configManager): static;

    public function calculateColumns(string $value, ?array $customColumnConfiguration = null): array;

    public function getColumnInfoForAdjuster(string $value, ?array $customColumnConfiguration = null);
}
