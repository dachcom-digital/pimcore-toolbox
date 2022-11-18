<?php

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model;
use Pimcore\Model\Element;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;

class ParallaxImage extends Model\Document\Editable\Relations
{
    public function getType(): string
    {
        return 'parallaximage';
    }

    public function setElements(): self
    {
        if (empty($this->elements)) {
            $this->elements = [];
            foreach ($this->elementIds as $elementId) {
                $el = Element\Service::getElementById($elementId['type'], $elementId['id']);
                if ($el instanceof Element\ElementInterface) {
                    $el->setProperty('parallaxPosition', 'text', $elementId['parallaxPosition']);
                    $el->setProperty('parallaxSize', 'text', $elementId['parallaxSize']);
                    $this->elements[] = $el;
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
            foreach ($this->elements as $element) {
                if ($element instanceof DataObject\Concrete) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'object',
                        $element->getClassName(),
                        $element->getProperty('parallaxPosition'),
                        $element->getProperty('parallaxSize'),
                    ];
                } elseif ($element instanceof DataObject\AbstractObject) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'object',
                        'folder',
                        $element->getProperty('parallaxPosition'),
                        $element->getProperty('parallaxSize'),
                    ];
                } elseif ($element instanceof Asset) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'asset',
                        $element->getType(),
                        $element->getProperty('parallaxPosition'),
                        $element->getProperty('parallaxSize'),
                    ];
                } elseif ($element instanceof Document) {
                    $return[] = [
                        $element->getId(),
                        $element->getRealFullPath(),
                        'document',
                        $element->getType(),
                        $element->getProperty('parallaxPosition'),
                        $element->getProperty('parallaxSize'),
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
                $obj = $element['obj'];
                if ($obj instanceof Element\ElementInterface) {
                    $elementType = Element\Service::getElementType($obj);
                    $key = $elementType . '_' . $obj->getId();
                    $dependencies[$key] = [
                        'id'   => $obj->getId(),
                        'type' => $elementType
                    ];
                }
            }
        }

        return $dependencies;
    }
}
