<?php

namespace Toolbox;

use Pimcore\Tool;

class Config {

    /**
     * @static
     * @return \Zend_Config
     */
    public static function getConfig() {

        $config = null;

        $configFile = PIMCORE_CONFIGURATION_DIRECTORY . "/toolbox-config.xml";

        if(\Zend_Registry::isRegistered('toolbox_config')) {

            $config = \Zend_Registry::get("toolbox_config");

        } else  {

            try {

                $config = new \Zend_Config_Xml($configFile);
                self::setConfig($config);

            } catch (\Exception $e) {

                \Logger::emergency("Cannot find system configuration, should be located at: " . $configFile);

                if(is_file( $configFile )) {

                    Tool::exitWithError("Your toolbox-config.xml located at " . $configFile . " is invalid, please check and correct it manually!");

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
