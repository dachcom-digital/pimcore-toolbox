<?php

namespace ToolboxBundle\Builder;

use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Translation\Translator;
use ToolboxBundle\Registry\StoreProviderRegistryInterface;
use Twig\Environment;

class BrickConfigBuilder
{
    protected Translator $translator;
    protected Environment $templating;
    protected StoreProviderRegistryInterface $storeProvider;

    public function __construct(
        Translator $translator,
        Environment $templating,
        StoreProviderRegistryInterface $storeProvider
    ) {
        $this->translator = $translator;
        $this->templating = $templating;
        $this->storeProvider = $storeProvider;
    }

    public function buildDialogBoxConfiguration(?Info $info, string $brickId, array $configNode = [], array $themeOptions = []): EditableDialogBoxConfiguration
    {
        $config = new EditableDialogBoxConfiguration();

        $configParameter = $configNode['config_parameter'] ?? [];
        $configElements = $configNode['config_elements'] ?? [];

        $configWindowSize = $this->getConfigWindowSize($configParameter);

        $config->setHeight($configWindowSize['height']);
        $config->setWidth($configWindowSize['width']);

        $config->setReloadOnClose($configParameter['reload_on_close'] ?? false);

        $defaultFields = [];
        $acFields = [];
        $configElements = $this->parseConfigElements($info, $brickId, $themeOptions, $configElements);

        foreach ($configElements as $configElement) {
            if ($configElement['additional_classes_element'] === true) {
                $acFields[] = $configElement;
            } else {
                $defaultFields[] = $configElement;
            }
        }

        $config->setItems(array_merge($defaultFields, $acFields));

        return $config;
    }

    private function getConfigWindowSize(array $configParameter): array
    {
        $configWindowSize = $configParameter['window_size'] ?? null;

        if (is_string($configWindowSize)) {
            return [
                'width'  => $configWindowSize === 'small' ? 600 : 800,
                'height' => $configWindowSize === 'small' ? 400 : 600,
            ];

        }

        if (is_array($configWindowSize)) {
            return [
                'width'  => $configWindowSize['width'],
                'height' => $configWindowSize['height'],
            ];
        }

        return [
            'width'  => 550,
            'height' => 370,
        ];
    }

    private function parseConfigElements(?Info $info, string $brickId, array $themeOptions, array $configElements): array
    {
        $editableNodes = [];

        if (empty($configElements)) {
            return $editableNodes;
        }

        $acStoreProcessed = false;

        foreach ($configElements as $configElementName => $elementData) {

            $editableNode = $this->parseConfigElement($info, $configElementName, $elementData, $acStoreProcessed);

            //if element need's a store and store is empty: skip field
            if ($this->needStore($elementData['type']) && $this->hasValidStore($editableNode['config']) === false) {
                continue;
            }

            $editableNodes[] = $editableNode;

            $editableNodes = $this->checkColumnAdjusterField($brickId, $themeOptions, $configElementName, $editableNodes);

            if ($elementData['type'] === 'additionalClasses') {
                $acStoreProcessed = true;
            }

        }

        return $editableNodes;
    }

    private function parseConfigElement(?Info $info, string $elementName, array $elementData, bool $acStoreProcessed): array
    {
        $editableConfig = $elementData['config'];
        $editableType = $elementData['type'];

        //set element config data
        $parsedNode = $this->parseElementNode($elementName, $elementData, $acStoreProcessed);

        //set width
        if ($this->canHaveDynamicWidth($editableType)) {
            $parsedNode['width'] = $parsedNode['width'] ?? '100%';
        } else {
            unset($parsedNode['width']);
        }

        //set height
        if ($this->canHaveDynamicHeight($editableType)) {
            $parsedNode['height'] = $parsedNode['height'] ?? 200;
        } else {
            unset($parsedNode['height']);
        }

        //set default
        $parsedNode['config']['value'] = $this->getSelectedValue($info, $parsedNode, $editableConfig['default'] ?? null);

        //check store
        if ($this->needStore($editableType) && $this->hasValidStore($editableConfig)) {
            $parsedNode['config']['store'] = $this->buildStore($editableType, $editableConfig);
        }

        return $parsedNode;
    }

    private function parseElementNode(string $configElementName, array $elementData, bool $acStoreProcessed): array
    {
        $elementNode = [
            'type'                       => $elementData['type'],
            'label'                      => isset($elementData['title']) && !empty($elementData['title']) ? $elementData['title'] : null,
            'config'                     => $elementData['config'] ?? [],
            'additional_classes_element' => false,
        ];

        if ($elementData['type'] === 'additionalClasses') {
            if ($acStoreProcessed === true) {
                throw new \Exception(
                    sprintf(
                        'A element of type "additionalClasses" in element "%s" already has been defined. You can only add one field of type "%s" per area. Use "%s" instead.',
                        $configElementName,
                        'additionalClasses',
                        'additionalClassesChained'
                    )
                );
            }

            $elementNode['type'] = 'select';
            $elementNode['label'] = isset($elementData['title']) && !empty($elementData['title']) ? $elementData['title'] : 'Additional';
            $elementNode['additional_classes_element'] = true;
            $elementName = 'add_classes';
        } elseif ($elementData['type'] === 'additionalClassesChained') {
            if ($acStoreProcessed === false) {
                throw new \Exception(
                    sprintf(
                        'You need to add a element of type "%s" before adding a "%s" element.',
                        'additionalClasses',
                        'additionalClassesChained'
                    )
                );
            } elseif (!str_starts_with($configElementName, 'additional_classes_chain_')) {
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

            $elementNode['type'] = 'select';
            $elementNode['label'] = isset($elementData['title']) && !empty($elementData['title']) ? $elementData['title'] : 'Additional';
            $elementNode['additional_classes_element'] = true;
            $elementName = 'add_cclasses_' . $chainedIncrementor;
        } else {
            $elementName = $configElementName;
        }

        //set config element name
        $elementNode['name'] = $elementName;

        //translate title
        if (!empty($elementData['label'])) {
            $elementNode['label'] = $this->translator->trans($elementData['label'], [], 'admin');
        }

        //translate description
        if (!empty($elementData['description'])) {
            $elementNode['description'] = $this->translator->trans($elementData['description'], [], 'admin');
        }

        return $elementNode;
    }

    private function getSelectedValue(?Info $info, array $config, mixed $defaultConfigValue): mixed
    {
        if (!$info instanceof Info) {
            return $config;
        }

        $el = $info->getDocumentElement($config['name'], $config['type']);

        if ($el === null) {
            return $config;
        }

        // force default (only if it returns false)
        // checkboxes may return an empty string and are impossible to track into default mode
        if (!empty($defaultConfigValue) && (method_exists($el, 'isEmpty') && $el->isEmpty() === true)) {
            $el->setDataFromResource($defaultConfigValue);
        }

        $value = $el instanceof Checkbox ? $el->isChecked() : $el->getData();

        return !empty($value) ? $value : $defaultConfigValue;
    }

    private function checkColumnAdjusterField(string $brickId, array $themeOptions, string $configElementName, array $editableNodes): array
    {
        if ($brickId !== 'columns') {
            return $editableNodes;
        }

        if ($configElementName !== 'type') {
            return $editableNodes;
        }

        if (empty($themeOptions['grid']['breakpoints'])) {
            return $editableNodes;
        }

        $editableNodes[] = [
            'type'                       => 'columnadjuster',
            'label'                      => null,
            'config'                     => [],
            'name'                       => 'columnadjuster',
            'additional_classes_element' => false,
        ];

        return $editableNodes;
    }

    private function buildStore($type, $config): array
    {
        $storeValues = [];
        if (isset($config['store']) && !is_null($config['store'])) {
            $storeValues = $config['store'];
        } elseif (isset($config['store_provider']) && !is_null($config['store_provider'])) {
            $storeProvider = $this->storeProvider->get($config['store_provider']);
            $storeValues = $storeProvider->getValues();
        }

        if (count($storeValues) === 0) {
            throw new \Exception($type . ' has no valid configured store');
        }

        $store = [];
        foreach ($storeValues as $k => $v) {
            if (is_array($v)) {
                $v = $v['name'];
            }
            $store[] = [$k, $this->translator->trans($v, [], 'admin')];
        }

        return $store;
    }

    private function hasValidStore($parsedConfig): bool
    {
        if (isset($parsedConfig['store']) && is_array($parsedConfig['store']) && count($parsedConfig['store']) > 0) {
            return true;
        }

        if (isset($parsedConfig['store_provider']) && $this->storeProvider->has($parsedConfig['store_provider'])) {
            return true;
        }

        return false;
    }

    private function needStore($type): bool
    {
        return in_array($type, ['select', 'multiselect', 'additionalClasses', 'additionalClassesChained']);
    }

    private function canHaveDynamicWidth($type): bool
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

    private function canHaveDynamicHeight($type): bool
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
}
