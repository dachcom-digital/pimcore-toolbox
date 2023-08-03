<?php

namespace ToolboxBundle\Service;

use ToolboxBundle\Manager\ConfigManagerInterface;

class DataAttributeService
{
    public function __construct(protected ConfigManagerInterface $configManager)
    {
    }

    public function generateDataAttributes(string $node, array $overrides = [], bool $ignoreNonExistingCoreAttributes = false): string
    {
        $values = $this->generateAttributeStack($node, $overrides, $ignoreNonExistingCoreAttributes);

        if ($values === null) {
            return '';
        }

        return $this->parseValues($values);
    }

    public function generateDataAttributesAsArray(string $node, array $overrides = [], bool $ignoreNonExistingCoreAttributes = false): array
    {
        $values = $this->generateAttributeStack($node, $overrides, $ignoreNonExistingCoreAttributes);

        if ($values === null) {
            return [];
        }

        return $values;
    }

    private function generateAttributeStack(string $node, array $overrides = [], bool $ignoreNonExistingCoreAttributes = false): ?array
    {
        $attributesNode = $this->configManager->getConfig('data_attributes');

        $coreAttributesAvailable = !empty($attributesNode[$node]['values']) && is_array($attributesNode[$node]['values']);

        if ($ignoreNonExistingCoreAttributes === false && $coreAttributesAvailable === false) {
            return null;
        }

        $coreAttributes = $coreAttributesAvailable === false ? [] : $attributesNode[$node]['values'];

        return array_merge($coreAttributes, $overrides);
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
        return preg_replace('/_/', '-', $input);
    }
}
