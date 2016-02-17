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
            $config = \Zend_Registry::get("toolbox_config");
        }
        else
        {
            $configFile = TOOLBOX_CONFIGURATION_FILE;

            try
            {
                $config = new \Zend_Config(include($configFile));
                self::setConfig($config);

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

    /**
     * @static
     * @param \Zend_Config $config
     * @return void
     */
    public static function setConfig (\Zend_Config $config) {

        \Zend_Registry::set("toolbox_config", $config);

    }
}

