<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element;

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

        if (count($this->elements) > 0) {
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

        if (count($this->elements) > 0) {
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

        if (count($this->elements) > 0) {
            foreach ($this->elements as $element) {
                $elementType = Element\Service::getElementType($element);
                $key = $elementType . '_' . $element->getId();
                $dependencies[$key] = [
                    'id'   => $element->getId(),
                    'type' => $elementType
                ];
            }
        }

        return $dependencies;
    }
}
