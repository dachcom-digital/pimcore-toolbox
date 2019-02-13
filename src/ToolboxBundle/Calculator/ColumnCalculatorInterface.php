<?php

namespace ToolboxBundle\Calculator;

use ToolboxBundle\Manager\ConfigManagerInterface;

interface ColumnCalculatorInterface
{
    /**
     * @param ConfigManagerInterface $configManager
     *
     * @return mixed
     */
    public function setConfigManager(ConfigManagerInterface $configManager);

    /**
     * @param string     $value
     * @param null|array $customColumnConfiguration
     *
     * @return array
     */
    public function calculateColumns($value, $customColumnConfiguration = null);

    /**
     * @param string     $value
     * @param null|array $customColumnConfiguration
     *
     * @return mixed
     */
    public function getColumnInfoForAdjuster($value, $customColumnConfiguration = null);
}
