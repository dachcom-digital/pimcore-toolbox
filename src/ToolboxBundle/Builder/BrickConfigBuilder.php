<?php

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Translation\Translator;
use Pimcore\Templating\Renderer\TagRenderer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class BrickConfigBuilder
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
     * @var bool
     */
    var $hasReload = FALSE;

    /**
     * @var bool
     */
    var $documentEditableId = FALSE;

    /**
     * @var string
     */
    var $documentEditableName = '';

    /**
     * @var \Pimcore\Model\Document\Tag\Area\Info null
     */
    var $info = NULL;

    /**
     * @var array
     */
    var $themeOptions = [];

    /**
     * @var array
     */
    var $configElements = [];

    /**
     * @var array
     */
    var $configParameter = [];

    /**
     * @var null
     */
    var $configWindowSize = NULL;

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
     * @param       $documentEditableId
     * @param       $documentEditableName
     * @param Info  $info
     * @param array $configNode
     * @param array $themeOptions
     *
     * @return string
     */
    public function buildElementConfig($documentEditableId, $documentEditableName, Info $info, $configNode = [], $themeOptions = [])
    {
        if ($info->getView()->get('editmode') === FALSE) {
            return FALSE;
        }

        $this->documentEditableId = $documentEditableId;
        $this->documentEditableName = $documentEditableName;
        $this->info = $info;
        $this->themeOptions = $themeOptions;
        $this->configElements = isset($configNode['config_elements']) ? $configNode['config_elements'] : [];
        $this->configParameter = isset($configNode['config_parameter']) ? $configNode['config_parameter'] : [];
        $this->configWindowSize = $this->getConfigWindowSize();

        $fieldSetArgs = [
            'config_elements'        => $this->parseConfigElements(),
            'document_editable_name' => $this->translator->trans($this->documentEditableName, [], 'admin'),
            'window_size'            => $this->configWindowSize,
            'document'               => $info->getDocument()
        ];

        return $this->templating->render('@Toolbox/Admin/AreaConfig/fieldSet.html.twig', $fieldSetArgs);
    }

    /**
     * @return null|string
     */
    private function getConfigWindowSize()
    {
        $configWindowSize = isset($this->configParameter['window_size']) ? (string)$this->configParameter['window_size'] : NULL;
        return !is_null($configWindowSize) ? $configWindowSize : 'small';
    }

    /**
     * @param $type
     * @return bool
     */
    private function needStore($type)
    {
        return in_array($type, ['select', 'multiselect', 'additionalClasses']);
    }

    /**
     * @param $type
     * @return bool
     */
    private function canHaveDynamicWidth($type)
    {
        return in_array($type,
            [
                'multihref',
                'href',
                'image',
                'input',
                'multiselect',
                'numeric',
                'embed',
                'pdf',
                'renderlet',
                'select',
                'snippet',
                'table',
                'textarea',
                'video',
                'wysiwyg',
                'parallaximage'
            ]);
    }

    /**
     * @param $type
     * @return bool
     */
    private function canHaveDynamicHeight($type)
    {
        return in_array($type, [
            'multihref',
            'width',
            'image',
            'multiselect',
            'embed',
            'pdf',
            'renderlet',
            'snippet',
            'textarea',
            'video',
            'wysiwyg',
            'parallaximage'
        ]);
    }

    /**
     * @param $type
     * @param $config
     * @return array
     * @throws \Exception
     */
    private function getTagConfig($type, $config)
    {
        if (is_null($config)) {
            return [];
        }

        $this->hasReload = isset($config['reload']) ? $config['reload'] === TRUE : TRUE;

        $parsedConfig = $config;

        //override reload
        $parsedConfig['reload'] = FALSE;

        //set width
        if ($this->canHaveDynamicWidth($type)) {
            $parsedConfig['width'] = isset($parsedConfig['width']) ? $parsedConfig['width'] : ($this->configWindowSize === 'large' ? 760 : 560);
        } else {
            unset($parsedConfig['width']);
        }

        //set height
        if ($this->canHaveDynamicHeight($type)) {
            $parsedConfig['height'] = isset($parsedConfig['height']) ? $parsedConfig['height'] : 200;
        } else {
            unset($parsedConfig['height']);
        }

        //check store
        if ($this->needStore($type) && isset($parsedConfig['store']) && !is_null($parsedConfig['store'])) {

            if (empty($parsedConfig['store'])) {
                throw new \Exception($type . ' (' . $this->documentEditableId . ') has no valid configured store');
            }

            $store = [];
            foreach ($parsedConfig['store'] as $k => $v) {

                if (is_array($v)) {
                    $v = $v['name'];
                }

                $store[] = [$k, $this->translator->trans($v, [], 'admin')];
            }

            $parsedConfig['store'] = $store;
        } else {
            unset($parsedConfig['store']);
        }

        return $parsedConfig;
    }

    /**
     * types: type, title, description, col_class, conditions
     *
     * @param $configElementName
     * @param $rawConfig
     *
     * @return array
     * @throws \Exception
     */
    private function getAdditionalConfig($configElementName, $rawConfig)
    {
        if (is_null($rawConfig)) {
            return [];
        }

        $config = $rawConfig;
        $defaultConfigValue = isset($config['config']['default']) ? $config['config']['default'] : NULL;

        //remove tag area config.
        unset($config['config']);

        //set element config data
        $parsedConfig = $this->parseElementConfig($configElementName, $config);

        //set default
        $parsedConfig = $this->getSelectedValue($parsedConfig, $defaultConfigValue);

        //set conditions to empty array.
        if (!isset($parsedConfig['conditions'])) {
            $parsedConfig['conditions'] = [];
        } elseif (!is_array($parsedConfig['conditions'])) {
            throw new \Exception('conditions configuration needs to be an array');
        }

        return $parsedConfig;
    }

    /**
     * @param $config
     * @param $defaultConfigValue
     *
     * @return mixed
     */
    private function getSelectedValue($config, $defaultConfigValue)
    {
        /** @var \Pimcore\Model\Document\Tag\* $el */
        $el = $this->tagRenderer->getTag($this->info->getDocument(), $config['type'], $config['name']);

        //force default (only if it returns false. checkboxes may return an empty string and are impossible to track into default mode
        if (!empty($defaultConfigValue) && ($el->isEmpty() === TRUE)) {
            $el->setDataFromResource($defaultConfigValue);
        }

        $value = NULL;

        switch ($config['type']) {

            case 'checkbox' :
                $value = $el->isChecked();
                break;
            default:
                $value = $el->getData();
        }

        $config['selected_value'] = !empty($value) ? $value : $defaultConfigValue;

        return $config;
    }

    /**
     * @param $configElementName
     * @param $elConf
     *
     * @return mixed
     */
    private function parseElementConfig($configElementName, $elConf)
    {
        if ($elConf['type'] === 'additionalClasses') {
            $elConf['type'] = 'select';
            $elConf['title'] = 'Additional';
            $elementName = 'add_classes';
        } else {
            $elementName = $configElementName;
        }

        //set config element name
        $elConf['name'] = $elementName;

        //set edit_reload to element reload setting
        $elConf['edit_reload'] = $this->hasReload;

        //set editmode hidden to false on initial state
        $elConf['editmode_hidden'] = FALSE;

        //translate title
        if (!empty($elConf['title'])) {
            $elConf['title'] = $this->translator->trans($elConf['title'], [], 'admin');
        }

        //translate description
        if (!empty($elConf['description'])) {
            $elConf['description'] = $this->translator->trans($elConf['description'], [], 'admin');
        }

        return $elConf;
    }

    /**
     * @return array
     */
    private function parseConfigElements()
    {
        $parsedConfig = [];
        if (empty($this->configElements)) {
            return $parsedConfig;
        }

        foreach ($this->configElements as $configElementName => $c) {
            $tagConfig = $c['config'];
            $parsedTagConfig = $this->getTagConfig($c['type'], $tagConfig);
            $parsedAdditionalConfig = $this->getAdditionalConfig($configElementName, $c);

            $parsedConfig[] = ['tag_config' => $parsedTagConfig, 'additional_config' => $parsedAdditionalConfig];
            $parsedConfig = $this->checkDependingSystemField($configElementName, $parsedConfig);

        }

        //condition needs to applied after all elements has been initialized!
        return self::checkCondition($parsedConfig);
    }

    /**
     * Add possible dynamic fields based on current field (like the column adjuster after the "type" field in field "columns"
     * @param $configElementName
     * @param $configFields
     * @return array
     */
    private function checkDependingSystemField($configElementName, $configFields)
    {
        // add column adjuster (only if breakpoints are defined!
        if ($this->documentEditableId === 'columns' && $configElementName === 'type') {
            if(empty($this->themeOptions['grid']['breakpoints'])) {
                return $configFields;
            }

            $parsedTagConfig = ['reload' => FALSE];
            $additionalConfig = [
                'type'            => 'columnadjuster',
                'editmode_hidden' => FALSE,
                'col_class'       => '',
                'name'            => 'columnadjuster',
                'title'           => NULL,
                'edit_reload'     => FALSE,
            ];
            $configFields[] = ['tag_config' => $parsedTagConfig, 'additional_config' => $additionalConfig];
        }

        return $configFields;
    }

    /**
     * @param $configElements
     * @return array
     */
    private function checkCondition($configElements)
    {
        $filteredData = [];

        if (empty($configElements)) {
            return $configElements;
        }

        foreach ($configElements as $configElementName => $el) {

            //no conditions? add it!
            if (empty($el['additional_config']['conditions'])) {
                $filteredData[] = $el;
                continue;
            }

            $orConditions = $el['additional_config']['conditions'];

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
                $filteredData[] = self::resetElement($el);
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
            if ($el['additional_config']['name'] === $name) {
                return $el['additional_config']['selected_value'];
            }
        }

        return NULL;
    }

    /**
     * @param $el
     *
     * @return mixed
     */
    private function resetElement($el)
    {
        $value = !empty($el['tag_config']['default']) ? $el['tag_config']['default'] : NULL;
        $this->tagRenderer->getTag($this->info->getDocument(), $el['additional_config']['type'], $el['additional_config']['name'])->setDataFromResource($value);
        $el['additional_config']['selected_value'] = $value;
        $el['additional_config']['editmode_hidden'] = TRUE;

        return $el;
    }
}