<?php

namespace ToolboxBundle\Calculator;

use ToolboxBundle\Manager\ConfigManagerInterface;

interface ColumnCalculatorInterface
{
    public function setConfigManager(ConfigManagerInterface $configManager): self;

    /**
     * @throws \Exception
     */
    public function calculateColumns(?string $value, ?array $customColumnConfiguration = null): array;

    /**
     * @throws \Exception
     */
    public function getColumnInfoForAdjuster(?string $value, ?array $customColumnConfiguration = null): bool|array;
}
