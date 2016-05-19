<?php

namespace Toolbox\Tools;

use Toolbox\Config;
use Pimcore\View;

class ElementBuilder {

    /**
     * @param $type
     * @param View $view
     *
     * @return string
     */
    public static function buildElementConfig($type, View $view)
    {
        $configElements = array();

        $configNode = Config::getConfig()->{$type};

        if( !empty( $configNode) )
        {
            $configElements = $configNode->configElements->toArray();
        }

        if( empty( $configElements ) )
        {
            return "";
        }

        $config = self::parseConfig($type, $configElements, $view);

        return $view->template('toolbox/admin/fieldSet.php', array('configElements' => $config));
    }

    private static function parseConfig( $type, $config, View $view )
    {
        $parsedConfig = array();

        foreach( $config as $c)
        {
            $elConf = array();
            $elValid = TRUE;

            if( !isset( $c['type']))
            {
                throw new \Exception('toolbox config type (' . $type. ') is not set.');
            }

            $elConf['type']     = $c['type'];
            $elConf['name']     = $c['name'];
            $elConf['title']    = $view->translateAdmin($c['title']);
            $elConf['reload']   = isset( $c['reload'] ) ? $c['reload'] : TRUE;
            $elConf['default']  = isset( $c['default'] ) ? $c['default'] : NULL;

            switch( $c['type'] )
            {
                case 'select':

                    if( !isset( $c['values']))
                    {
                        throw new \Exception('toolbox config value (' . $type. ') is not set.');
                    }

                    $store = array();

                    foreach( $c['values'] as $k => $v)
                    {
                        $store[] = array($k, $view->translateAdmin($v));
                    }

                    $elConf['store'] = $store;
                    break;

                case 'additionalClasses':

                    if( empty( $c['values']  ) )
                    {
                        $elValid = FALSE;
                    }

                    $store = array();
                    $store[] = array('default', $view->translateAdmin('Default'));

                    foreach( $c['values'] as $k => $v)
                    {
                        $store[] = array($k, $v);
                    }

                    $elConf['type']  = 'select';
                    $elConf['name']  = $type . 'AdditionalClasses';
                    $elConf['title']  = $view->translateAdmin('Additional');
                    $elConf['reload'] = TRUE;
                    $elConf['store'] = $store;

                    if( is_null($elConf['default']) )
                    {
                        $elConf['default'] = 'default';
                    }

                    break;

                case 'input':
                    break;
                case 'checkbox':

                    break;

                default:
                    throw new \Exception( $c['type']  . ' is not a valid toolbox config element');
            }

            if( $elValid )
            {
                $parsedConfig[] = $elConf;
            }

            //print_r($parsedConfig);
        }

        return $parsedConfig;

    }
}