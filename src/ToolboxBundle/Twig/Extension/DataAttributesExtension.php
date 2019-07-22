<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataAttributesExtension extends AbstractExtension
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @param ConfigManagerInterface $configManager
     */
    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('toolbox_data_attributes_generator', [$this, 'generateDataAttributes']),
        ];
    }

    /**
     * @param string $node
     * @param array  $overrides
     * @param bool   $ignoreNonExistingCoreAttributes
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateDataAttributes($node, $overrides = [], $ignoreNonExistingCoreAttributes = false)
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

    /**
     * @param array $values
     *
     * @return string
     */
    private function parseValues($values)
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

    /**
     * @param string $input
     *
     * @return mixed
     */
    private function lineToDash($input)
    {
        return preg_replace('/_/', '-', $input);
    }
}
