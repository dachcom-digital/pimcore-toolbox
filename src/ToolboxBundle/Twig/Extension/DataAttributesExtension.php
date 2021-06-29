<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataAttributesExtension extends AbstractExtension
{
    protected ConfigManagerInterface $configManager;

    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_data_attributes_generator', [$this, 'generateDataAttributes']),
        ];
    }

    public function generateDataAttributes(string $node, array $overrides = [], bool $ignoreNonExistingCoreAttributes = false): string
    {
        $attributesNode = $this->configManager->getConfig('data_attributes');

        $coreAttributesAvailable = isset($attributesNode[$node]['values']) && is_array($attributesNode[$node]['values']) && !empty($attributesNode[$node]['values']);

        if ($ignoreNonExistingCoreAttributes === false && $coreAttributesAvailable === false) {
            return '';
        }

        $coreAttributes = $coreAttributesAvailable === false ? [] : $attributesNode[$node]['values'];

        $values = array_merge($coreAttributes, $overrides);

        return $this->parseValues($values);
    }

    private function parseValues(array $values): string
    {
        $attributes = [];

        foreach ($values as $key => $value) {
            //continue if real empty
            if (!is_bool($value) && (($value === 0 || $value) === false)) {
                continue;
            }

            if (is_array($value)) {
                $parsedValue = htmlspecialchars(json_encode($value));
            } elseif (is_bool($value)) {
                $parsedValue = $value ? 'true' : 'false';
            } elseif (is_object($value)) {
                $parsedValue = get_class($value);
            } else {
                $parsedValue = $value;
            }

            $attributes[] = 'data-' . $this->lineToDash($key) . '="' . $parsedValue . '"';
        }

        return implode(' ', $attributes);
    }

    private function lineToDash(string $input): string
    {
        return str_replace("_", '-', $input);
    }
}
