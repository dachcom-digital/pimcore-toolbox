<?php

namespace Toolbox;

use Pimcore\Tool;

class Config {

    /**
     * @static
     * @return \Zend_Config
     */
    public static function getConfig() {

        $config = NULL;

        if(\Zend_Registry::isRegistered('toolbox_config'))
        {
            $config = \Zend_Registry::get('toolbox_config');
        }
        else
        {
            $configFile = TOOLBOX_CONFIGURATION_FILE;

            try
            {
                $config = new \Zend_Config(include($configFile));
                self::setConfig($config, 'toolbox_config');

            }
            catch (\Exception $e)
            {
                \Logger::emergency("Cannot find system configuration, should be located at: " . $configFile);

                if(is_file( $configFile ))
                {
                    Tool::exitWithError("Your toolbox_configuration.php located at " . $configFile . " is invalid, please check and correct it manually!");
                }
            }
        }

        return $config;

    }

    public static function getCoreConfig() {

        $config = NULL;

        if(\Zend_Registry::isRegistered('toolbox_core_config'))
        {
            $config = \Zend_Registry::get('toolbox_core_config');
        }
        else
        {
            $configFile = TOOLBOX_CORE_CONFIGURATION_FILE;

            $config = new \Zend_Config(include($configFile));
            self::setConfig($config, 'toolbox_core_config');
        }

        return $config;

    }

    /**
     * @static
     * @param \Zend_Config $config
     * @param string $name
     * @return void
     */
    public static function setConfig (\Zend_Config $config, $name) {

        \Zend_Registry::set($name, $config);

    }
}

