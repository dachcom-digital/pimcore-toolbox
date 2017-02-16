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
        $configWindowSize = NULL;

        if (!empty($configNode)) {
            $userConfigElements = $configNode->configElements->toArray();
            $configWindowSize = isset($configNode->windowSize) ? (string)$configNode->windowSize : NULL;
        }

        $coreConfigNode = [];
        $coreConfig = Config::getCoreConfig();

        if (isset($coreConfig->{$type}) && isset($coreConfig->{$type}->configElements)) {
            $userElementNames = array_column($userConfigElements, 'name');
            //check if user wants to override or disable core element. remove it then!
            foreach ($coreConfig->{$type}->configElements->toArray() as $coreConfigElement) {

                $coreIndex = array_search($coreConfigElement['name'], $userElementNames);
                if ($coreIndex === FALSE) {
                    $coreConfigNode[] = $coreConfigElement;
                }
            }

            if (is_null($configWindowSize) && isset($coreConfig->{$type}->windowSize)) {
                $configWindowSize = (string)$coreConfig->{$type}->windowSize;
            }
        }

        //merge and remove NULL type elements
        $configElements = array_filter(array_merge($userConfigElements, $coreConfigNode), function ($el) {
            return !is_null($el['type']);
        });

        if (empty($configElements)) {
            return '';
        }

        $windowSize = !is_null($configWindowSize) ? $configWindowSize : 'small';
        $config = self::parseConfig($type, $configElements, $windowSize, $view);

        return $view->template('admin/fieldSet.php', ['configElements' => $config, 'windowSize' => $windowSize]);
    }

    /**
     * @param      $type
     * @param      $config
     * @param View $view
     * @param      $windowSize
     *
     * @return array
     * @throws \Exception
     */
    private static function parseConfig($type, $config, $windowSize = 'small', View $view)
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
            $elConf['reload'] = FALSE;
            $elConf['edit-reload'] = isset($c['reload']) ? $c['reload'] : TRUE;
            $elConf['default'] = isset($c['default']) ? $c['default'] : NULL;
            $elConf['description'] = isset($c['description']) ? $c['description'] : NULL;
            $elConf['col-class'] = isset($c['col-class']) ? $c['col-class'] : 't-col-full';

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
                    $elConf['width'] = isset($c['width']) ? $c['width'] : ($windowSize === 'large' ? 760 : 560);

                    //force default
                    if (!empty($elConf['default']) && $view->select($elConf['name'])->isEmpty()) {
                        $view->select($elConf['name'])->setDataFromResource($elConf['default']);
                    }

                    $value = $view->select($elConf['name'])->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'checkbox':

                    $value = $view->checkbox($elConf['name'])->isChecked();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'input':

                    $elConf['width'] = isset($c['width']) ? $c['width'] : ($windowSize === 'large' ? 760 : 560);

                    $value = $view->input($elConf['name'])->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'numeric':

                    $elConf['width'] = isset($c['width']) ? $c['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['minValue'] = isset($c['minValue']) ? $c['minValue'] : '';
                    $elConf['maxValue'] = isset($c['maxValue']) ? $c['maxValue'] : '';
                    $elConf['decimalPrecision'] = isset($c['decimalPrecision']) ? $c['decimalPrecision'] : FALSE;
                    $elConf['class'] = isset($c['class']) ? $c['class'] : '';

                    //force default
                    if (!empty($elConf['default']) && $view->numeric($elConf['name'])->isEmpty()) {
                        $view->numeric($elConf['name'])->setDataFromResource($elConf['default']);
                    }

                    $value = $view->numeric($elConf['name'])->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'href':

                    $elConf['width'] = isset($c['width']) ? $c['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['uploadPath'] = isset($c['uploadPath']) ? $c['uploadPath'] : '';
                    $elConf['types'] = isset($c['types']) ? $c['types'] : NULL;
                    $elConf['subtypes'] = isset($c['subtypes']) ? $c['subtypes'] : NULL;
                    $elConf['classes'] = isset($c['classes']) ? $c['classes'] : NULL;
                    $elConf['class'] = isset($c['class']) ? $c['class'] : '';
                    break;

                case 'multihref':

                    $elConf['width'] = isset($c['width']) ? $c['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['height'] = isset($c['height']) ? $c['height'] : 200;
                    $elConf['uploadPath'] = isset($c['uploadPath']) ? $c['uploadPath'] : '';
                    $elConf['types'] = isset($c['types']) ? $c['types'] : NULL;
                    $elConf['subtypes'] = isset($c['subtypes']) ? $c['subtypes'] : NULL;
                    $elConf['classes'] = isset($c['classes']) ? $c['classes'] : NULL;
                    $elConf['class'] = isset($c['class']) ? $c['class'] : '';
                    break;

                case 'parallaximage':

                    $positionStore = isset($c['position']) ? $c['position'] : [];

                    $store = [];
                    $store['default'] = $view->translateAdmin('Default');

                    foreach ($positionStore as $k => $v) {
                        $store[$k] = $view->translateAdmin($v);
                    }

                    $elConf['position'] = $store;
                    $elConf['width'] = isset($c['width']) ? $c['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['height'] = isset($c['height']) ? $c['height'] : 200;
                    $elConf['class'] = isset($c['class']) ? $c['class'] : '';
                    break;

                case 'additionalClasses':

                    if (empty($c['values'])) {
                        $elValid = FALSE;
                    }

                    $store = [];
                    $store[] = ['default', $view->translateAdmin('Default')];

                    foreach ($c['values'] as $k => $v) {
                        $store[] = [$k, $view->translateAdmin($v)];
                    }

                    $elConf['type'] = 'select';
                    $elConf['name'] = $type . 'AdditionalClasses';
                    $elConf['title'] = $view->translateAdmin('Additional');
                    $elConf['reload'] = FALSE;
                    $elConf['edit-reload'] = isset($c['reload']) ? $c['reload'] : TRUE;
                    $elConf['store'] = $store;

                    if (is_null($elConf['default'])) {
                        $elConf['default'] = 'default';
                    }

                    //force default
                    if ($view->select($type . 'AdditionalClasses')->isEmpty()) {
                        $defaultVal = !empty($elConf['default']) ? $elConf['default'] : 'default';
                        $view->select($type . 'AdditionalClasses')->setDataFromResource($defaultVal);
                    }

                    $value = $view->select($type . 'AdditionalClasses')->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
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