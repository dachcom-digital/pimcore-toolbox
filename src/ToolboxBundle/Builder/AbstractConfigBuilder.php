<?php

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Templating\Renderer\EditableRenderer;
use Pimcore\Translation\Translator;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Registry\StoreProviderRegistryInterface;
use Twig\Environment;

abstract class AbstractConfigBuilder
{
    public function __construct(
        protected Translator $translator,
        protected Environment $templating,
        protected StoreProviderRegistryInterface $storeProvider,
        protected AreaManagerInterface $areaManager,
        protected EditableRenderer $editableRenderer
    ) {
    }

    protected function parseConfigElements(?Info $info, string $brickId, array $themeOptions, array $configElements, array $tabs): array
    {
        $editableNodes = [];

        if (empty($configElements)) {
            return $editableNodes;
        }

        $acStoreProcessed = false;

        foreach ($configElements as $configElementName => $elementData) {
            $editableNode = $this->parseConfigElement($info, $configElementName, $elementData, $acStoreProcessed, $brickId, $themeOptions);

            //if element need's a store and store is empty: skip field
            if ($this->needStore($elementData['type']) && $this->hasValidStore($editableNode['config']) === false) {
                continue;
            }

            $editableNodes[] = $editableNode;

            $editableNodes = $this->checkColumnAdjusterField($brickId, $elementData['tab'], $themeOptions, $configElementName, $editableNodes);

            if ($elementData['type'] === 'additionalClasses') {
                $acStoreProcessed = true;
            }
        }

        // move additional classes to bottom
        $defaultFields = [];
        $acFields = [];

        foreach ($editableNodes as $editableNode) {
            if ($editableNode['additional_classes_element'] === true) {
                $acFields[] = $editableNode;
            } else {
                $defaultFields[] = $editableNode;
            }
        }

        $editableNodes = array_merge($defaultFields, $acFields);

        // assign tabs, if configured
        if (count($tabs) > 0) {
            $tabbedEditableNodes = [];
            foreach ($tabs as $tabId => $tabName) {
                $tabbedEditableNodes[] = [
                    'type'  => 'panel',
                    'title' => $this->translator->trans($tabName, [], 'admin'),
                    'items' => array_values(
                        array_filter($editableNodes, static function ($editableNode) use ($tabId) {
                            return $editableNode['tab'] === $tabId;
                        })
                    )
                ];
            }

            return [
                'type'  => 'tabpanel',
                'items' => $tabbedEditableNodes
            ];
        }

        return $editableNodes;
    }

    private function parseConfigElement(?Info $info, string $elementName, array $elementData, bool $acStoreProcessed, string $brickId, array $themeOptions): array
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

        // set default
        $parsedNode['config']['defaultValue'] = $this->getSelectedValue($info, $parsedNode, $editableConfig['default'] ?? null);

        // check store
        if ($this->needStore($editableType) && $this->hasValidStore($editableConfig)) {
            $parsedNode['config']['store'] = $this->buildStore($editableType, $editableConfig);
        }

        if ($editableType === 'block' && array_key_exists('children', $elementData) && is_array($elementData['children']) && count($elementData['children']) > 0) {
            $parsedNode['children'] = $this->parseConfigElements($info, $brickId, $themeOptions, $elementData['children'], []);
        }

        return $parsedNode;
    }

    private function parseElementNode(string $configElementName, array $elementData, bool $acStoreProcessed): array
    {
        $elementNode = [
            'type'                       => $elementData['type'],
            'name'                       => $configElementName,
            'tab'                        => $elementData['tab'],
            'label'                      => !empty($elementData['title']) ? $elementData['title'] : null,
            'description'                => !empty($elementData['description']) ? $elementData['description'] : null,
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
            $elementNode['label'] = !empty($elementData['title']) ? $elementData['title'] : 'Additional';
            $elementNode['additional_classes_element'] = true;
            $elementNode['name'] = 'add_classes';
        } elseif ($elementData['type'] === 'additionalClassesChained') {
            if ($acStoreProcessed === false) {
                throw new \Exception(
                    sprintf(
                        'You need to add a element of type "%s" before adding a "%s" element.',
                        'additionalClasses',
                        'additionalClassesChained'
                    )
                );
            }

            if (!str_starts_with($configElementName, 'additional_classes_chain_')) {
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
            $elementNode['label'] = !empty($elementData['title']) ? $elementData['title'] : 'Additional';
            $elementNode['additional_classes_element'] = true;
            $elementNode['name'] = 'add_cclasses_' . $chainedIncrementor;
        }

        // translate title
        if (!empty($elementNode['label'])) {
            $elementNode['label'] = $this->translator->trans($elementNode['label'], [], 'admin');
        }

        // translate description
        if (!empty($elementNode['description'])) {
            $elementNode['description'] = $this->translator->trans($elementNode['description'], [], 'admin');
        }

        return $elementNode;
    }

    private function getSelectedValue(?Info $info, array $config, mixed $defaultConfigValue): mixed
    {
        if (!$info instanceof Info) {
            return $this->castPimcoreEditableValue($config['type'], $defaultConfigValue);
        }

        $el = $info->getDocumentElement($config['name'], $config['type']);

        if ($el === null) {
            return $this->castPimcoreEditableValue($config['type'], $defaultConfigValue);
        }

        // force default (only if it returns false)
        // checkboxes may return an empty string and are impossible to track into default mode
        if (!empty($defaultConfigValue) && (method_exists($el, 'isEmpty') && $el->isEmpty() === true)) {
            $el->setDataFromResource($defaultConfigValue);
        }

        $value = $el instanceof Checkbox ? $el->isChecked() : $el->getData();

        $fallbackAwareValue = !empty($value) ? $value : $defaultConfigValue;

        return $this->castPimcoreEditableValue($config['type'], $fallbackAwareValue);
    }

    private function checkColumnAdjusterField(string $brickId, ?string $tab, array $themeOptions, string $configElementName, array $editableNodes): array
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
            'name'                       => 'columnadjuster',
            'tab'                        => $tab,
            'label'                      => $this->translator->trans('Column adjuster', [], 'admin'),
            'config'                     => [],
            'additional_classes_element' => false,
        ];

        return $editableNodes;
    }

    private function buildStore($type, $config): array
    {
        $storeValues = [];
        if (isset($config['store'])) {
            $storeValues = $config['store'];
        } elseif (isset($config['store_provider'])) {
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

    private function castPimcoreEditableValue(string $type, mixed $value): mixed
    {
        // pimcore numeric editable requires string type
        if ($type === 'numeric') {
            return $value === null ? null : (string) $value;
        }

        if ($type === 'areablock') {
            return $value === null ? null : (is_string($value) ? $value : serialize($value));
        }

        return $value;
    }
}
