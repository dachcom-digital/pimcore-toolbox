<?php

namespace ToolboxBundle\Service;

class ConfigManager
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function setConfig($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param $section
     *
     * @return mixed
     */
    public function getConfig($section)
    {
        return $this->config[$section];
    }

    /**
     * @param string $areaName
     *
     * @return mixed
     */
    public function getAreaConfig($areaName = '')
    {
        return $this->config['areas'][$areaName];
    }

    /**
     * @param string $areaName
     * @param string $configElementName
     *
     * @return mixed
     */
    public function getAreaElementConfig($areaName = '', $configElementName = '')
    {
        return $this->config['areas'][$areaName]['configElements'][$configElementName];
    }
}