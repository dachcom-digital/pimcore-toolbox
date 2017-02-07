<?php

namespace Toolbox\Tool;

use Toolbox\Config;
use Pimcore\View;

class ElementBuilder
{
    /**
     * @param      $type
     * @param View $view
     *
     * @return string
     */
    public static function buildElementConfig($type, View $view)
    {
        $userConfigElements = [];

        $configNode = Config::getConfig()->{$type};

        if (!empty($configNode)) {
            $userConfigElements = $configNode->configElements->toArray();
        }

        if (empty($userConfigElements)) {
            $userConfigElements = [];
        }

        $coreConfigNode = [];
        $coreConfig = Config::getCoreConfig();

        if (isset($coreConfig->{$type}) && isset($coreConfig->{$type}->configElements)) {
            $coreConfigNode = $coreConfig->{$type}->configElements->toArray();
        }

        $configElements = array_merge($userConfigElements, $coreConfigNode);

        if (empty($configElements)) {
            return '';
        }

        $config = self::parseConfig($type, $configElements, $view);

        return $view->template('admin/fieldSet.php', ['configElements' => $config]);
    }

    /**
     * @param      $type
     * @param      $config
     * @param View $view
     *
     * @return array
     * @throws \Exception
     */
    private static function parseConfig($type, $config, View $view)
    {
        $parsedConfig = [];

        foreach ($config as $c) {
            $elConf = [];
            $elValid = TRUE;

            if (!isset($c['type'])) {
                throw new \Exception('toolbox config type (' . $type . ') is not set.');
            }

            $elConf['type'] = $c['type'];
            $elConf['name'] = $c['name'];
            $elConf['title'] = $view->translateAdmin($c['title']);
            $elConf['reload'] = isset($c['reload']) ? $c['reload'] : TRUE;
            $elConf['default'] = isset($c['default']) ? $c['default'] : NULL;

            if (isset($c['conditions'])) {
                $elConf['conditions'] = $c['conditions'];
            }

            switch ($c['type']) {
                case 'select':

                    if (!isset($c['values'])) {
                        throw new \Exception('toolbox config value (' . $type . ') is not set.');
                    }

                    $store = [];

                    foreach ($c['values'] as $k => $v) {
                        $store[] = [$k, $view->translateAdmin($v)];
                    }

                    $elConf['store'] = $store;

                    $value = $view->select($elConf['name'])->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'additionalClasses':

                    if (empty($c['values'])) {
                        $elValid = FALSE;
                    }

                    $store = [];
                    $store[] = ['default', $view->translateAdmin('Default')];

                    foreach ($c['values'] as $k => $v) {
                        $store[] = [$k, $v];
                    }

                    $elConf['type'] = 'select';
                    $elConf['name'] = $type . 'AdditionalClasses';
                    $elConf['title'] = $view->translateAdmin('Additional');
                    $elConf['reload'] = TRUE;
                    $elConf['store'] = $store;

                    if (is_null($elConf['default'])) {
                        $elConf['default'] = 'default';
                    }

                    $value = $view->select($type . 'AdditionalClasses')->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];

                    break;

                case 'checkbox':

                    $value = $view->checkbox($elConf['name'])->isChecked();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'input':

                    $value = $view->input($elConf['name'])->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];

                    $elConf['width'] = isset($c['width']) ? $c['width'] : 150;

                    break;

                default:
                    throw new \Exception($c['type'] . ' is not a valid toolbox config element');
            }

            if ($elValid) {
                $parsedConfig[] = $elConf;
            }
        }

        $parsedConfig = self::checkCondition($parsedConfig);

        return $parsedConfig;
    }

    /**
     * @param $configElements
     *
     * @return array
     */
    private static function checkCondition($configElements)
    {
        $filteredData = [];

        if (empty($configElements)) {
            return $configElements;
        }

        foreach ($configElements as $el) {
            if (isset($el['conditions'])) {
                $orConditions = $el['conditions'];

                $orGroup = [];
                $orState = FALSE;

                foreach ($orConditions as $andConditions) {
                    $andGroup = [];
                    $andState = TRUE;

                    foreach ($andConditions as $andConditionKey => $andConditionValue) {
                        $andGroup[] = self::getElementState($andConditionKey, $configElements) == $andConditionValue;
                    }

                    if (in_array(FALSE, $andGroup, TRUE)) {
                        $andState = FALSE;
                    }

                    $orGroup[] = $andState;
                }

                if (in_array(TRUE, $orGroup, TRUE)) {
                    $orState = TRUE;
                }

                if ($orState === TRUE) {
                    $filteredData[] = $el;
                }
            } else {
                $filteredData[] = $el;
            }
        }

        return $filteredData;
    }

    /**
     * @param string $name
     * @param        $elements
     *
     * @return null
     */
    private static function getElementState($name = '', $elements)
    {
        if (empty($elements)) {
            return NULL;
        }

        foreach ($elements as $el) {
            if ($el['name'] === $name) {
                return $el['__selectedValue'];
            }
        }

        return NULL;
    }
}