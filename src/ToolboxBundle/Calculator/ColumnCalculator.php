<?php

namespace ToolboxBundle\Calculator;

use ToolboxBundle\Manager\ConfigManager;

class ColumnCalculator implements ColumnCalculatorInterface
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @param ConfigManager $configManager
     * @return $this
     */
    public function setConfigManager(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
        return $this;
    }

    public function calculateColumns($value, $customColumnConfiguration = NULL)
    {
        throw new \Exception('please define a valid column calculator in toolbox.theme.calculator configuration tree');
    }

    public function getColumnInfoForAdjuster($value, $customColumnConfiguration = NULL)
    {
        throw new \Exception('please define a valid column calculator in toolbox.theme.calculator configuration tree');
    }
}