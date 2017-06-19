<?php

namespace ToolboxBundle\Service;

use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Translation\Translator;
use Pimcore\Templating\Renderer\TagRenderer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class ElementBuilder
{
    /**
     * @var Translator
     */
    var $translator;

    /**
     * @var TagRenderer
     */
    var $tagRenderer;

    /**
     * @var EngineInterface
     */
    var $templating;

    /**
     * ElementBuilder constructor.
     *
     * @param Translator      $translator
     * @param TagRenderer     $tagRenderer
     * @param EngineInterface $templating
     */
    public function __construct(
        Translator $translator,
        TagRenderer $tagRenderer,
        EngineInterface $templating
    ) {
        $this->translator = $translator;
        $this->tagRenderer = $tagRenderer;
        $this->templating = $templating;
    }

    /**
     * @param      $type
     * @param      $name
     * @param Info $info
     * @param array $configNode
     *
     * @return string
     */
    public function buildElementConfig($type, $name, Info $info, $configNode = [])
    {
        if ($info->getView()->get('editmode') === FALSE) {
            return FALSE;
        }

        $userConfigElements = [];
        $configWindowSize = NULL;

        if (!empty($configNode)) {
            $userConfigElements = $configNode['configElements'];
            $configWindowSize = isset($configNode['windowSize']) ? (string)$configNode['windowSize'] : NULL;
        }

        $coreConfigNode = [];

        if (isset($configNode[$type]) && isset($configNode[$type]['configElements'])) {
            $userElementNames = array_column($userConfigElements, 'name');
            //check if user wants to override or disable core element. remove it then!
            foreach ($configNode[$type]['configElements'] as $coreConfigElement) {
                $coreIndex = array_search($coreConfigElement['name'], $userElementNames);
                if ($coreIndex === FALSE) {
                    $coreConfigNode[] = $coreConfigElement;
                }
            }

            if (is_null($configWindowSize) && isset($coreConfig[$type]['windowSize'])) {
                $configWindowSize = (string)$coreConfig[$type]['windowSize'];
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
        $config = self::parseConfig($type, $configElements, $windowSize, $info);

        $fieldSetArgs = [
            'configElements' => $config,
            'elementTitle'   => $name,
            'windowSize'     => $windowSize,
            'document'       => $info->getDocument()
        ];

        return $this->templating->render('@Toolbox/Admin/AreaConfig/fieldSet.html.twig', $fieldSetArgs);
    }

    /**
     * @param                                       $type
     * @param                                       $config
     * @param \Pimcore\Model\Document\Tag\Area\Info $info
     * @param                                       $windowSize
     *
     * @return array
     * @throws \Exception
     */
    private function parseConfig($type, $config, $windowSize = 'small', $info)
    {
        $parsedConfig = [];

        foreach ($config as $configElementName => $c) {

            $elConf = [];
            $elValid = TRUE;

            if (!isset($c['type'])) {
                throw new \Exception('areaBrick  type for ' . $type . ' is not defined.');
            }

            if (!isset($c['config'])) {
                throw new \Exception('areaBrick configuration for ' . $type . ' is not defined.');
            }

            $elConf['type'] = $c['type'];
            $elConf['name'] = $configElementName;
            $elConf['title'] = isset($c['config']['title']) && !empty($c['config']['title']) ? $this->translator->trans($c['config']['title'], [], 'admin') : NULL;
            $elConf['reload'] = FALSE;
            $elConf['edit-reload'] = isset($c['config']['reload']) ? $c['config']['reload'] : TRUE;
            $elConf['default'] = isset($c['config']['default']) ? $c['config']['default'] : NULL;
            $elConf['description'] = isset($c['config']['description']) && !empty($c['config']['description']) ? $this->translator->trans($c['config']['description'], [], 'admin') : NULL;
            $elConf['col-class'] = isset($c['config']['col-class']) && !empty($c['config']['col-class']) ? $c['config']['col-class'] : 't-col-full';
            $elConf['editmode-hidden'] = FALSE;

            if (isset($c['config']['conditions'])) {
                $elConf['conditions'] = $c['config']['conditions'];
            }

            switch ($c['type']) {

                case 'select':

                    if (!isset($c['config']['values'])) {
                        throw new \Exception('toolbox config value (' . $type . ') is not set.');
                    }

                    $store = [];
                    foreach ($c['config']['values'] as $k => $v) {

                        if (is_array($v)) {
                            $v = $v['name'];
                        }

                        $store[] = [$k, $this->translator->trans($v, [], 'admin')];
                    }

                    $elConf['store'] = $store;
                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);

                    $el = $this->tagRenderer->getTag($info->getDocument(), 'select', $elConf['name']);

                    //force default
                    if (!empty($elConf['default']) && $el->isEmpty()) {
                        $el->setDataFromResource($elConf['default']);
                    }

                    $value = $el->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'checkbox':

                    $el = $this->tagRenderer->getTag($info->getDocument(), 'checkbox', $elConf['name']);

                    $value = $el->isChecked();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'input':

                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);

                    $el = $this->tagRenderer->getTag($info->getDocument(), 'input', $elConf['name']);

                    //force default
                    if (!empty($elConf['default']) && $el->isEmpty()) {
                        $el->setDataFromResource($elConf['default']);
                    }

                    $value = $el->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'numeric':

                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['minValue'] = isset($c['config']['minValue']) ? $c['config']['minValue'] : '';
                    $elConf['maxValue'] = isset($c['config']['maxValue']) ? $c['config']['maxValue'] : '';
                    $elConf['decimalPrecision'] = isset($c['config']['decimalPrecision']) ? $c['config']['decimalPrecision'] : FALSE;
                    $elConf['class'] = isset($c['config']['class']) ? $c['config']['class'] : '';

                    //force default
                    $el = $this->tagRenderer->getTag($info->getDocument(), 'numeric', $elConf['name']);

                    if (!empty($elConf['default']) && $el->isEmpty()) {
                        $el->setDataFromResource($elConf['default']);
                    }

                    $value = $el->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $elConf['default'];
                    break;

                case 'href':

                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['uploadPath'] = isset($c['config']['uploadPath']) ? $c['config']['uploadPath'] : '';
                    $elConf['types'] = isset($c['config']['types']) ? $c['config']['types'] : NULL;
                    $elConf['subtypes'] = isset($c['config']['subtypes']) ? $c['config']['subtypes'] : NULL;
                    $elConf['classes'] = isset($c['config']['classes']) ? $c['config']['classes'] : NULL;
                    $elConf['class'] = isset($c['config']['class']) ? $c['config']['class'] : '';
                    break;

                case 'multihref':

                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['height'] = isset($c['config']['height']) ? $c['config']['height'] : 200;
                    $elConf['uploadPath'] = isset($c['config']['uploadPath']) ? $c['config']['uploadPath'] : '';
                    $elConf['types'] = isset($c['config']['types']) ? $c['config']['types'] : NULL;
                    $elConf['subtypes'] = isset($c['config']['subtypes']) ? $c['config']['subtypes'] : NULL;
                    $elConf['classes'] = isset($c['config']['classes']) ? $c['config']['classes'] : NULL;
                    $elConf['class'] = isset($c['config']['class']) ? $c['config']['class'] : '';
                    break;

                case 'parallaximage':

                    $positionStore = isset($c['config']['position']) ? $c['config']['position'] : [];
                    $sizeStore = isset($c['config']['size']) ? $c['config']['size'] : [];

                    $elConf['position'] = [];
                    $elConf['position']['default'] = $this->translator->trans('Default', [], 'admin');

                    foreach ($positionStore as $k => $v) {
                        $elConf['position'][$k] = $this->translator->trans($v, [], 'admin');
                    }

                    $elConf['size'] = [];
                    $elConf['size']['default'] = $this->translator->trans('Default', [], 'admin');

                    foreach ($sizeStore as $k => $v) {
                        $elConf['size'][$k] = $this->translator->trans($v, [], 'admin');
                    }

                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['height'] = isset($c['config']['height']) ? $c['config']['height'] : 200;
                    $elConf['class'] = isset($c['config']['class']) ? $c['config']['class'] : '';
                    break;

                case 'additionalClasses':

                    if (empty($c['config']['values'])) {
                        $elValid = FALSE;
                    }

                    $store = [];
                    $store[] = ['default', $this->translator->trans('Default', [], 'admin')];

                    foreach ($c['config']['values'] as $k => $v) {
                        $store[] = [$k, $this->translator->trans($v, [], 'admin')];
                    }

                    $elConf['type'] = 'select';
                    $elConf['width'] = isset($c['config']['width']) ? $c['config']['width'] : ($windowSize === 'large' ? 760 : 560);
                    $elConf['name'] = $type . 'AdditionalClasses';
                    $elConf['title'] = $this->translator->trans('Additional', [], 'admin');
                    $elConf['reload'] = FALSE;
                    $elConf['edit-reload'] = isset($c['config']['reload']) ? $c['config']['reload'] : TRUE;
                    $elConf['store'] = $store;

                    if (is_null($elConf['default'])) {
                        $elConf['default'] = 'default';
                    }

                    $el = $this->tagRenderer->getTag($info->getDocument(), 'select', ($type . 'AdditionalClasses'));

                    //force default
                    $defaultVal = !empty($elConf['default']) ? $elConf['default'] : 'default';
                    if ($el->isEmpty()) {
                        $defaultVal = !empty($elConf['default']) ? $elConf['default'] : 'default';
                        $el->setDataFromResource($defaultVal);
                    }

                    $value = $el->getData();
                    $elConf['__selectedValue'] = !empty($value) ? $value : $defaultVal;
                    break;

                default:
                    throw new \Exception($c['type'] . ' is not a valid toolbox config element');
            }

            if ($elValid) {
                $parsedConfig[] = $elConf;
            }
        }

        //condition needs to applied after all elements has been initialized!
        $parsedConfig = self::checkCondition($parsedConfig, $info);

        return $parsedConfig;
    }

    /**
     * @param $configElements
     * @param $info
     *
     * @return array
     */
    private function checkCondition($configElements, $info)
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
                } else {
                    //we need to reset value, if possible!
                    self::resetElement($el, $info);
                    $el['editmode-hidden'] = TRUE;
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
    private function getElementState($name = '', $elements)
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

    /**
     * @param $el
     * @param $info
     *
     * @return string
     */
    private function resetElement($el, $info)
    {
        $value = !empty($el['default']) ? $el['default'] : NULL;

        switch ($el['type']) {
            case 'select':
                $el = $this->tagRenderer->getTag($info->getDocument(), 'select', $el['name'])->setDataFromResource($value);
                $elConf['__selectedValue'] = $value;
                break;
            case 'checkbox':
                $el = $this->tagRenderer->getTag($info->getDocument(), 'checkbox', $el['name'])->setDataFromResource($value);
                $elConf['__selectedValue'] = $value;
                break;
            case 'input':
                $el = $this->tagRenderer->getTag($info->getDocument(), 'input', $el['name'])->setDataFromResource($value);
                $elConf['__selectedValue'] = $value;
                break;
            case 'numeric':
                $el = $this->tagRenderer->getTag($info->getDocument(), 'numeric', $el['name'])->setDataFromResource($value);
                $elConf['__selectedValue'] = $value;
                break;
        }

        return $el;
    }
}