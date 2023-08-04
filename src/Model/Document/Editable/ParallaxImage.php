<?php

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model;
use Pimcore\Model\Element;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;

class ParallaxImage extends Model\Document\Editable\Relations
{
    protected array $parallaxProperties = [];

    public function getParallaxProperties(): array
    {
        return $this->parallaxProperties;
    }

    public function getParallaxPropertyByIndex(int $index)
    {
        return $this->parallaxProperties[$index] ?? [];
    }

    public function getType(): string
    {
        return 'parallaximage';
    }

    public function setElements(): static
    {
        if (empty($this->elements)) {
            $this->elements = [];
            foreach ($this->elementIds as $elementId) {
                $el = Element\Service::getElementById($elementId['type'], $elementId['id']);
                if ($el instanceof Element\ElementInterface) {
                    $this->elements[] = $el;
                    $this->parallaxProperties[] = [
                        'parallaxPosition' => $elementId['parallaxPosition'],
                        'parallaxSize'     => $elementId['parallaxSize']
                    ];
                }
            }
        }

        return $this;
    }

    public function getDataEditmode(): array
    {
        $this->setElements();
        $return = [];

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $index => $element) {
                if ($element instanceof DataObject\Concrete) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'object',
                        $element->getClassName(),
                        $this->parallaxProperties[$index]['parallaxPosition'] ?? null,
                        $this->parallaxProperties[$index]['parallaxSize'] ?? null,
                    ];
                } elseif ($element instanceof DataObject\AbstractObject) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'object',
                        'folder',
                        $this->parallaxProperties[$index]['parallaxPosition'] ?? null,
                        $this->parallaxProperties[$index]['parallaxSize'] ?? null,
                    ];
                } elseif ($element instanceof Asset) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'asset',
                        $element->getType(),
                        $this->parallaxProperties[$index]['parallaxPosition'] ?? null,
                        $this->parallaxProperties[$index]['parallaxSize'] ?? null,
                    ];
                } elseif ($element instanceof Document) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'document',
                        $element->getType(),
                        $this->parallaxProperties[$index]['parallaxPosition'] ?? null,
                        $this->parallaxProperties[$index]['parallaxSize'] ?? null,
                    ];
                }
            }
        }

        return $return;
    }

    public function frontend(): string
    {
        $this->setElements();
        $return = '';

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                $return .= Element\Service::getElementType($element['obj']) . ': ' . $element['obj']->getFullPath() . '<br />';
            }
        }

        return $return;
    }

    public function resolveDependencies(): array
    {
        $this->setElements();
        $dependencies = [];

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                if ($element instanceof Element\ElementInterface) {
                    $elementType = Element\Service::getElementType($element);
                    $key = $elementType . '_' . $element->getId();
                    $dependencies[$key] = [
                        'id'   => $element->getId(),
                        'type' => $elementType
                    ];
                }
            }
        }

        return $dependencies;
    }
}
