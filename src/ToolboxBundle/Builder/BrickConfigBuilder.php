<?php

namespace ToolboxBundle\Builder;

use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Model\Document\Editable\Area\Info;

class BrickConfigBuilder extends AbstractConfigBuilder implements BrickConfigBuilderInterface
{
    public function buildConfiguration(?Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $isInlineContext = false): EditableDialogBoxConfiguration
    {
        $config = new EditableDialogBoxConfiguration();

        $configParameter = $areaConfig['config_parameter'] ?? [];
        $configElements = $areaConfig['config_elements'] ?? [];
        $tabs = $areaConfig['tabs'] ?? [];

        $configWindowSize = $this->getConfigWindowSize($configParameter);

        $config->setHeight($configWindowSize['height']);
        $config->setWidth($configWindowSize['width']);
        $config->setReloadOnClose($configParameter['reload_on_close'] ?? false);

        $items = $this->parseConfigElements($info, $brickId, $themeOptions, $configElements, $tabs, true, $isInlineContext);

        $config->setItems($items);

        return $config;
    }

    public function buildConfigurationData(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $isInlineContext = false): array
    {
        $data = [];
        $invalidTypes = ['additionalClasses', 'additionalClassesChained'];
        $configElements = $areaConfig['config_elements'] ?? [];

        foreach ($configElements as $itemName => $elementData) {

            if ($isInlineContext === true && array_key_exists('inline_rendered', $elementData) && $elementData['inline_rendered'] === true) {
                continue;
            }

            if (in_array($elementData['type'], $invalidTypes, true)) {
                continue;
            }

            $data[$itemName] = $this->editableRenderer->getEditable($info->getDocument(), $elementData['type'], $itemName, [], false)->getData();
        }

        return $data;
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
}
