<?php

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Templating\Renderer\EditableRenderer;
use Pimcore\Translation\Translator;
use Symfony\Component\Templating\EngineInterface;
use ToolboxBundle\Registry\StoreProviderRegistryInterface;

class BrickConfigBuilder
{
    protected Translator $translator;
    protected EditableRenderer $tagRenderer;
    protected EngineInterface $templating;
    protected string $documentEditableId = '';
    protected string $documentEditableName = '';
    protected ?Info $info = null;
    protected array $themeOptions = [];
    protected array $configElements = [];
    protected array $configParameter = [];
    protected bool $hasAdditionalClassStore = false;
    protected ?string $configWindowSize = null;
    protected ?StoreProviderRegistryInterface $storeProvider = null;

    public function __construct(
        Translator $translator,
        EditableRenderer $tagRenderer,
        EngineInterface $templating,
        StoreProviderRegistryInterface $storeProvider
    ) {
        $this->translator = $translator;
        $this->tagRenderer = $tagRenderer;
        $this->templating = $templating;
        $this->storeProvider = $storeProvider;
    }

    public function buildElementConfig(string $documentEditableId, string $documentEditableName, Info $info, array $configNode = [], array $themeOptions = []): string
    {
        $fieldSetArgs = $this->buildElementConfigArguments($documentEditableId, $documentEditableName, $info, $configNode, $themeOptions);

        if (empty($fieldSetArgs)) {
            return '';
        }

        return $this->templating->render('@Toolbox/Admin/AreaConfig/fieldSet.html.twig', $fieldSetArgs);
    }

    public function buildElementConfigArguments(string $documentEditableId, string $documentEditableName, Info $info, array $configNode = [], array $themeOptions = []): array
    {
        $this->reset();

        if ($info->getParam('editmode') === false) {
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
            'brick_id'               => $info->getId()
        ];

        return $fieldSetArgs;
    }

    private function getConfigWindowSize(): ?string
    {
        $configWindowSize = isset($this->configParameter['window_size']) ? (string) $this->configParameter['window_size'] : null;

        return !is_null($configWindowSize) ? $configWindowSize : 'small';
    }

    private function needStore(string $type): bool
    {
        return in_array($type, ['select', 'multiselect', 'additionalClasses', 'additionalClassesChained']);
    }

    private function hasValidStore(array $parsedConfig): bool
    {
        if (isset($parsedConfig['store']) && is_array($parsedConfig['store']) && count($parsedConfig['store']) > 0) {
            return true;
        }

        if (isset($parsedConfig['store_provider']) && $this->storeProvider->has($parsedConfig['store_provider'])) {
            return true;
        }

        return false;
    }

    private function buildStore(string $type, array $config): array
    {
        $dataConfig = $config;

        unset($dataConfig['store'], $dataConfig['store_provider']);

        $storeValues = [];
        if (isset($config['store']) && !is_null($config['store'])) {
            $storeValues = $config['store'];
        } elseif (isset($config['store_provider']) && !is_null($config['store_provider'])) {
            $storeProvider = $this->storeProvider->get($config['store_provider']);
            $storeValues = $storeProvider->getValues();
        }

        if (count($storeValues) === 0) {
            throw new \Exception($type . ' (' . $this->documentEditableId . ') has no valid configured store');
        }

        $store = [];
        foreach ($storeValues as $k => $v) {
            if (is_array($v)) {
                $v = $v['name'];
            }
            $store[] = [$k, $this->translator->trans($v, [], 'admin')];
        }

        $dataConfig['store'] = $store;

        return $dataConfig;
    }

    private function canHaveDynamicWidth(string $type): bool
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

    private function canHaveDynamicHeight(string $type): bool
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

    private function reset(): void
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

    private function getTagConfig(string $type, array $config, array $additionalConfig): array
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
        if ($this->needStore($type) && $this->hasValidStore($parsedConfig)) {
            $parsedConfig = $this->buildStore($type, $parsedConfig);
        }

        return $parsedConfig;
    }

    /**
     * types: type, title, description, col_class, conditions.
     */
    private function getAdditionalConfig(string $configElementName, array $rawConfig): array
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

    private function getSelectedValue(array $config, $defaultConfigValue)
    {
        /** @var \Pimcore\Model\Document\Editable\EditableInterface $el */
        $el = $this->tagRenderer->getEditable($this->info->getDocument(), $config['type'], $config['name']);

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

    private function parseElementConfig(string $configElementName, array $elConf): array
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

    private function parseConfigElements(): array
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
     */
    private function checkDependingSystemField(string $configElementName, array $configFields): array
    {
        // add column adjuster (only if breakpoints are defined!
        if ($this->documentEditableId === 'columns' && $configElementName === 'type') {
            if (empty($this->themeOptions['grid']['breakpoints'])) {
                return $configFields;
            }

            $parsedTagConfig = ['reload' => false];
            $additionalConfig = [
                'type'                       => 'columnadjuster',
                'editmode_hidden'            => false,
                'col_class'                  => '',
                'name'                       => 'columnadjuster',
                'title'                      => null,
                'edit_reload'                => false,
                'additional_classes_element' => false
            ];

            $configFields[] = ['tag_config' => $parsedTagConfig, 'additional_config' => $additionalConfig];
        }

        return $configFields;
    }

    private function checkCondition(array $configElements): array
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

    private function getElementState(string $name = '', array $elements = []): ?string
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

    private function resetElement(array $el): array
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
