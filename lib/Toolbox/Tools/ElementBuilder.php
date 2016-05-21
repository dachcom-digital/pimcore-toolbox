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

            if( isset( $c['conditions'] ))
            {
                $elConf['conditions'] = $c['conditions'];
            }

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

                    $value = $view->select($elConf['name'])->getData();
                    $elConf['__selectedValue'] = !empty( $value ) ? $value : $elConf['default'];
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

                    $value = $view->select($type . 'AdditionalClasses')->getData();
                    $elConf['__selectedValue'] = !empty( $value ) ? $value : $elConf['default'];

                    break;

                case 'checkbox':

                    $value = $view->checkbox( $elConf['name'])->isChecked();
                    $elConf['__selectedValue'] = !empty( $value ) ? $value : $elConf['default'];
                    break;

                default:
                    throw new \Exception( $c['type']  . ' is not a valid toolbox config element');
            }

            if( $elValid )
            {
                $parsedConfig[] = $elConf;
            }
        }

        $parsedConfig = self::checkCondition($parsedConfig);

        return $parsedConfig;

    }

    private static function checkCondition($configElements)
    {
        $filteredData = array();

        if( empty( $configElements ) )
        {
            return $configElements;
        }

        foreach( $configElements as $el)
        {
            if( isset( $el['conditions'] ) )
            {
                $orConditions = $el['conditions'];

                $orGroup = array();
                $orState = FALSE;

                foreach( $orConditions as $andConditions)
                {
                    $andGroup = array();
                    $andState = TRUE;

                    foreach( $andConditions as $andConditionKey => $andConditionValue)
                    {
                        $andGroup[] = self::getElementState($andConditionKey, $configElements) == $andConditionValue;
                    }

                    if( in_array(false, $andGroup, true))
                    {
                        $andState = FALSE;
                    }

                    $orGroup[] = $andState;

                }

                if( in_array(true, $orGroup, true))
                {
                    $orState = TRUE;
                }

                if( $orState === TRUE)
                {
                    $filteredData[] = $el;
                }

            }
            else
            {
                $filteredData[] = $el;
            }
        }

        return $filteredData;

    }

    private static function getElementState($name = '', $elements)
    {
        if( empty( $elements ) )
        {
            return NULL;
        }

        foreach( $elements as $el)
        {
            if( $el['name'] === $name)
            {
                return $el['__selectedValue'];
            }
        }

        return NULL;

    }
}