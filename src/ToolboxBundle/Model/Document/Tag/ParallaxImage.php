<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Model\Element;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;

class ParallaxImage extends Model\Document\Tag\Relations
{
    /**
     * Return the type of the element.
     *
     * @return string
     */
    public function getType()
    {
        return 'parallaximage';
    }

    /**
     * @return $this
     */
    public function setElements()
    {
        if (empty($this->elements)) {
            $this->elements = [];
            foreach ($this->elementIds as $elementId) {
                $el = Element\Service::getElementById($elementId['type'], $elementId['id']);
                if ($el instanceof Element\ElementInterface) {
                    $this->elements[] = [
                        'obj'              => $el,
                        'parallaxPosition' => $elementId['parallaxPosition'],
                        'parallaxSize'     => $elementId['parallaxSize']
                    ];
                }
            }
        }

        return $this;
    }

    /**
     * Converts the data so it's suitable for the editmode.
     *
     * @return mixed
     */
    public function getDataEditmode()
    {
        $this->setElements();
        $return = [];

        if (is_array($this->elements) && count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                $obj = $element['obj'];
                if ($obj instanceof DataObject\Concrete) {
                    $return[] = [
                        $obj->getId(),
                        $obj->getRealFullPath(),
                        'object',
                        $obj->getClassName(),
                        $element['parallaxPosition'],
                        $element['parallaxSize']
                    ];
                } elseif ($obj instanceof DataObject\AbstractObject) {
                    $return[] = [
                        $obj->getId(),
                        $obj->getRealFullPath(),
                        'object',
                        'folder',
                        $element['parallaxPosition'],
                        $element['parallaxSize']
                    ];
                } elseif ($obj instanceof Asset) {
                    $return[] = [
                        $obj->getId(),
                        $obj->getRealFullPath(),
                        'asset',
                        $obj->getType(),
                        $element['parallaxPosition'],
                        $element['parallaxSize']
                    ];
                } elseif ($obj instanceof Document) {
                    $return[] = [
                        $obj->getId(),
                        $obj->getRealFullPath(),
                        'document',
                        $obj->getType(),
                        $element['parallaxPosition'],
                        $element['parallaxSize']
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * @see Document\Tag\TagInterface::frontend
     *
     * @return string
     */
    public function frontend()
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

    /**
     * @return array
     */
    public function resolveDependencies()
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
