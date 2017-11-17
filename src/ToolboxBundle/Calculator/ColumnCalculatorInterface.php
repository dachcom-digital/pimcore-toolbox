<?php

namespace ToolboxBundle\Calculator;

use ToolboxBundle\Manager\ConfigManager;

interface ColumnCalculatorInterface
{
    /**
     * @param ConfigManager $configManager
     * @return mixed
     */
    public function setConfigManager(ConfigManager $configManager);

    /**
     * @param string     $value
     * @param null|array $customColumnConfiguration
     *
     * @return array
     */
    public function calculateColumns($value, $customColumnConfiguration = NULL);

    /**
     * @param string     $value
     * @param null|array $customColumnConfiguration
     * @return mixed
     */
    public function getColumnInfoForAdjuster($value, $customColumnConfiguration = NULL);
}