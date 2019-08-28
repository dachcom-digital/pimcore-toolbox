<?php

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Translation\Translator;
use Pimcore\Templating\Renderer\TagRenderer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class BrickConfigBuilder
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var TagRenderer
     */
    protected $tagRenderer;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var string
     */
    protected $documentEditableId = '';

    /**
     * @var string
     */
    protected $documentEditableName = '';

    /**
     * @var \Pimcore\Model\Document\Tag\Area\Info null
     */
    protected $info = null;

    /**
     * @var array
     */
    protected $themeOptions = [];

    /**
     * @var array
     */
    protected $configElements = [];

    /**
     * @var array
     */
    protected $configParameter = [];

    /**
     * @var bool
     */
    protected $hasAdditionalClassStore = false;

    /**
     * @var null|string
     */
    protected $configWindowSize = null;

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
     * @param string $documentEditableId
     * @param string $documentEditableName
     * @param Info   $info
     * @param array  $configNode
     * @param array  $themeOptions
     *
     * @return string
     *
     * @throws \Exception
     */
    public function buildElementConfig($documentEditableId, $documentEditableName, Info $info, $configNode = [], $themeOptions = [])
    {
        $fieldSetArgs = $this->buildElementConfigArguments($documentEditableId, $documentEditableName, $info, $configNode, $themeOptions);

        if (empty($fieldSetArgs)) {
            return '';
        }

        return $this->templating->render('@Toolbox/Admin/AreaConfig/fieldSet.html.twig', $fieldSetArgs);
    }

    /**
     * @param string $documentEditableId
     * @param string $documentEditableName
     * @param Info   $info
     * @param array  $configNode
     * @param array  $themeOptions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function buildElementConfigArguments($documentEditableId, $documentEditableName, Info $info, $configNode = [], $themeOptions = [])
    {
        $this->reset();

        if ($info->getView()->get('editmode') === false) {
            return [];
        }

        $this->documentEditableId = $documentEditableId;
        $this->documentEditableName = $documentEditableName;
        $this->info = $info;
        $this->themeOptions = $themeOptions;
        $this->configElements = isset($configNode['config_elements']) ? $configNode['config_elements'] : [];
        $this->configParameter = isset($configNode['config_parameter']) ? $configNode['config_parameter'] : [];
        $this->configWindowSize = $this->getConfigWindowSize();

        $defaultFields = [];
        $acFields = [];
        $configElements = $this->parseConfigElements();

        foreach ($configElements as $configElement) {
            if ($configElement['additional_config']['additional_classes_element'] === true) {
                $acFields[] = $configElement;
            } else {
                $defaultFields[] = $configElement;
            }
        }

        $fieldSetArgs = [
            'config_elements'        => array_merge($defaultFields, $acFields),
            'document_editable_name' => $this->translator->trans($this->documentEditableName, [], 'admin'),
            'window_size'            => $this->configWindowSize,
            'document'               => $info->getDocument(),
            'brick_id'               => $info->id
        ];

        return $fieldSetArgs;
    }

    /**
     * @return null|string
     */
    private function getConfigWindowSize()
    {
        $configWindowSize = isset($this->configParameter['window_size']) ? (string) $this->configParameter['window_size'] : null;

        return !is_null($configWindowSize) ? $configWindowSize : 'small';
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function needStore($type)
    {
        return in_array($type, ['select', 'multiselect', 'additionalClasses', 'additionalClassesChained']);
    }

    /**
     * @param array $parsedConfig
     *
     * @return bool
     */
    private function hasValidStore($parsedConfig)
    {
        return isset($parsedConfig['store']) && is_array($parsedConfig['store']) && count($parsedConfig['store']) > 0;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function canHaveDynamicWidth($type)
    {
        return in_array(
            $type,
            [
                'multihref',
                'relations',
                'href',
                'relation',
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
                'parallaximage',
                'additionalClasses',
                'additionalClassesChained'
            ]
        );
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function canHaveDynamicHeight($type)
    {
        return in_array($type, [
            'multihref',
            'relations',
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
     * Reset class for next element to build.
     */
    private function reset()
    {
        $this->documentEditableId = '';
        $this->documentEditableName = '';
        $this->info = null;
        $this->themeOptions = [];
        $this->configElements = [];
        $this->configParameter = [];
        $this->hasAdditionalClassStore = false;
        $this->configWindowSize = null;
    }

    /**
     * @param string $type
     * @param array  $config
     * @param array  $additionalConfig
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getTagConfig($type, $config, $additionalConfig)
    {
        if (is_null($config)) {
            return [];
        }

        $parsedConfig = $config;

        //override reload
        $parsedConfig['reload'] = false;

        //set width
        if ($this->canHaveDynamicWidth($type)) {
            $parsedConfig['width'] = isset($parsedConfig['width'])
                ? $parsedConfig['width']
                : (isset($additionalConfig['col_class']) ? '100%' : ($this->configWindowSize === 'large' ? 760 : 560));
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
     * types: type, title, description, col_class, conditions.
     *
     * @param string $configElementName
     * @param array  $rawConfig
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getAdditionalConfig($configElementName, $rawConfig)
    {
        if (is_null($rawConfig)) {
            return [];
        }

        $config = $rawConfig;
        $defaultConfigValue = isset($config['config']['default']) ? $config['config']['default'] : null;

        //remove tag area config.
        unset($config['config']);

        //set element config data
        $parsedConfig = $this->parseElementConfig($configElementName, $config);

        $parsedConfig['edit_reload'] = isset($rawConfig['config']['reload']) ? $rawConfig['config']['reload'] === true : true;

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
     * @param array $config
     * @param mixed $defaultConfigValue
     *
     * @return mixed
     */
    private function getSelectedValue($config, $defaultConfigValue)
    {
        /** @var \Pimcore\Model\Document\Tag\TagInterface $el */
        $el = $this->tagRenderer->getTag($this->info->getDocument(), $config['type'], $config['name']);

        // force default (only if it returns false)
        // checkboxes may return an empty string and are impossible to track into default mode
        if (!empty($defaultConfigValue) && (method_exists($el, 'isEmpty') && $el->isEmpty() === true)) {
            $el->setDataFromResource($defaultConfigValue);
        }

        $value = null;
        if ($el instanceof Checkbox) {
            $value = $el->isChecked();
        } else {
            $value = $el->getData();
        }

        $config['selected_value'] = !empty($value) ? $value : $defaultConfigValue;

        return $config;
    }

    /**
     * @param string $configElementName
     * @param array  $elConf
     *
     * @return array
     *
     * @throws \Exception
     */
    private function parseElementConfig($configElementName, $elConf)
    {
        $elConf['additional_classes_element'] = false;

        if ($elConf['type'] === 'additionalClasses') {
            if ($this->hasAdditionalClassStore === true) {
                throw new \Exception(
                    sprintf(
                        'A element of type "additionalClasses" in element "%s" already has been defined. You can only add one field of type "%s" per area. Use "%s" instead.',
                        $this->documentEditableName,
                        'additionalClasses',
                        'additionalClassesChained'
                    )
                );
            }

            $elConf['type'] = 'select';
            $elConf['title'] = isset($elConf['title']) && !empty($elConf['title']) ? $elConf['title'] : 'Additional';
            $elConf['col_class'] = isset($elConf['col_class']) && !empty($elConf['col_class']) ? $elConf['col_class'] : 't-col-third';
            $elConf['additional_classes_element'] = true;
            $elementName = 'add_classes';
            $this->hasAdditionalClassStore = true;
        } elseif ($elConf['type'] === 'additionalClassesChained') {
            if ($this->hasAdditionalClassStore === false) {
                throw new \Exception(
                    sprintf(
                        'You need to add a element of type "%s" before adding a "%s" element.',
                        'additionalClasses',
                        'additionalClassesChained'
                    )
                );
            } elseif (substr($configElementName, 0, 25) !== 'additional_classes_chain_') {
                throw new \Exception(
                    sprintf(
                        'Chained AC element name needs to start with "%s" followed by a numeric. "%s" given.',
                        'additional_classes_chain_',
                        $configElementName
                    )
                );
            }

            $chainedElementName = explode('_', $configElementName);
            $chainedIncrementor = end($chainedElementName);
            if (!is_numeric($chainedIncrementor)) {
                throw new \Exception('Chained AC element name must end with an numeric. "' . $chainedIncrementor . '" given.');
            }

            $elConf['type'] = 'select';
            $elConf['title'] = isset($elConf['title']) && !empty($elConf['title']) ? $elConf['title'] : 'Additional';
            $elConf['col_class'] = isset($elConf['col_class']) && !empty($elConf['col_class']) ? $elConf['col_class'] : 't-col-third';
            $elConf['additional_classes_element'] = true;
            $elementName = 'add_cclasses_' . $chainedIncrementor;
        } else {
            $elementName = $configElementName;
        }

        //set config element name
        $elConf['name'] = $elementName;

        //set editmode hidden to false on initial state
        $elConf['editmode_hidden'] = false;

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
     *
     * @throws \Exception
     */
    private function parseConfigElements()
    {
        $parsedConfig = [];
        if (empty($this->configElements)) {
            return $parsedConfig;
        }

        foreach ($this->configElements as $configElementName => $c) {
            $tagConfig = $c['config'];
            $parsedAdditionalConfig = $this->getAdditionalConfig($configElementName, $c);
            $parsedTagConfig = $this->getTagConfig($c['type'], $tagConfig, $parsedAdditionalConfig);

            //if element need's a store and store is empty: skip field
            if ($this->needStore($c['type']) && $this->hasValidStore($parsedTagConfig) === false) {
                continue;
            }

            $parsedConfig[] = ['tag_config' => $parsedTagConfig, 'additional_config' => $parsedAdditionalConfig];
            $parsedConfig = $this->checkDependingSystemField($configElementName, $parsedConfig);
        }

        //condition needs to applied after all elements has been initialized!
        return $this->checkCondition($parsedConfig);
    }

    /**
     * Add possible dynamic fields based on current field (like the column adjuster after the "type" field in field "columns".
     *
     * @param string $configElementName
     * @param array  $configFields
     *
     * @return array
     */
    private function checkDependingSystemField($configElementName, $configFields)
    {
        // add column adjuster (only if breakpoints are defined!
        if ($this->documentEditableId === 'columns' && $configElementName === 'type') {
            if (empty($this->themeOptions['grid']['breakpoints'])) {
                return $configFields;
            }

            $parsedTagConfig = ['reload' => false];
            $additionalConfig = [
                'type'            => 'columnadjuster',
                'editmode_hidden' => false,
                'col_class'       => '',
                'name'            => 'columnadjuster',
                'title'           => null,
                'edit_reload'     => false,
            ];
            $configFields[] = ['tag_config' => $parsedTagConfig, 'additional_config' => $additionalConfig];
        }

        return $configFields;
    }

    /**
     * @param array $configElements
     *
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
            $orState = false;

            foreach ($orConditions as $andConditions) {
                $andGroup = [];
                $andState = true;

                foreach ($andConditions as $andConditionKey => $andConditionValue) {
                    $andGroup[] = $this->getElementState($andConditionKey, $configElements) == $andConditionValue;
                }

                if (in_array(false, $andGroup, true)) {
                    $andState = false;
                }

                $orGroup[] = $andState;
            }

            if (in_array(true, $orGroup, true)) {
                $orState = true;
            }

            if ($orState === true) {
                $filteredData[] = $el;
            } else {
                //we need to reset value, if possible!
                $filteredData[] = $this->resetElement($el);
            }
        }

        return $filteredData;
    }

    /**
     * @param string $name
     * @param array  $elements
     *
     * @return null|string
     */
    private function getElementState($name = '', $elements = [])
    {
        if (empty($elements)) {
            return null;
        }

        foreach ($elements as $el) {
            if ($el['additional_config']['name'] === $name) {
                return $el['additional_config']['selected_value'];
            }
        }

        return null;
    }

    /**
     * @param array $el
     *
     * @return mixed
     */
    private function resetElement($el)
    {
        $value = !empty($el['tag_config']['default']) ? $el['tag_config']['default'] : null;

        $this->tagRenderer->getTag(
            $this->info->getDocument(),
            $el['additional_config']['type'],
            $el['additional_config']['name']
        )->setDataFromResource($value);

        $el['additional_config']['selected_value'] = $value;
        $el['additional_config']['editmode_hidden'] = true;

        return $el;
    }
}
