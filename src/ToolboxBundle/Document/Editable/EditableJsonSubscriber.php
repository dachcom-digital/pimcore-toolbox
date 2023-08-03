<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ToolboxBundle\Event\HeadlessElementEvent;
use ToolboxBundle\ToolboxEvents;

final class EditableJsonSubscriber implements EventSubscriberInterface
{
    protected const ELEMENTS_IDENTIFIER = 'elements';
    protected const ELEMENT_TYPE_IDENTIFIER = 'elementType';
    protected const ELEMENT_SUB_TYPE_IDENTIFIER = 'elementSubType';
    protected const ELEMENT_DATA_IDENTIFIER = 'elementContext';

    protected array $jsonEditables = [];

    public static function getSubscribedEvents(): array
    {
        return [
            ToolboxEvents::HEADLESS_ELEMENT_STACK_ADD => 'onHeadlessElementAdd',
        ];
    }

    public function onHeadlessElementAdd(HeadlessElementEvent $event): void
    {
        $this->jsonEditables[$event->getElementNamespace()] = [$event->getElementType(), $event->getElementSubType(), $event->getData()];
    }

    public function getJsonEditables(): array
    {
        $convertedEditables = $this->convertNestedArray($this->jsonEditables);

        $this->sortNestedArray($convertedEditables);
        $this->simplifyNestedArray($convertedEditables);

        return $convertedEditables;
    }

    private function convertNestedArray($flatArray): array
    {
        $nestedArray = [];

        foreach ($flatArray as $key => $value) {

            $keys = explode(':', $key);
            $currentArray = &$nestedArray;

            foreach ($keys as $nestedKey) {
                /** @phpstan-ignore-next-line */
                if (!isset($currentArray[self::ELEMENTS_IDENTIFIER][$nestedKey])) {
                    $currentArray[self::ELEMENTS_IDENTIFIER][$nestedKey] = [];
                }

                $currentArray = &$currentArray[self::ELEMENTS_IDENTIFIER][$nestedKey];
            }

            $currentArray[self::ELEMENT_TYPE_IDENTIFIER] = $value[0];
            $currentArray[self::ELEMENT_SUB_TYPE_IDENTIFIER] = $value[1];
            $currentArray[self::ELEMENT_DATA_IDENTIFIER] = $value[2];
        }

        /** @phpstan-ignore-next-line */
        return $nestedArray[self::ELEMENTS_IDENTIFIER] ?? [];
    }

    private function simplifyNestedArray(&$array): void
    {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as &$value) {

            if (
                is_array($value) &&
                count($value) === 1 &&
                isset($value[self::ELEMENTS_IDENTIFIER]) &&
                !array_is_list($value[self::ELEMENTS_IDENTIFIER])
            ) {
                $value = $value[self::ELEMENTS_IDENTIFIER];
            }

            $this->simplifyNestedArray($value);
        }
    }

    private function sortNestedArray(&$array): void
    {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as &$item) {

            if (!is_array($item)) {
                continue;
            }

            if (isset($item[self::ELEMENTS_IDENTIFIER]) && is_array($item[self::ELEMENTS_IDENTIFIER])) {
                $isNumericKeyedArray = array_unique(array_map('is_numeric', array_keys($item[self::ELEMENTS_IDENTIFIER]))) === [true];
                if ($isNumericKeyedArray) {
                    $item[self::ELEMENTS_IDENTIFIER] = array_values($item[self::ELEMENTS_IDENTIFIER]);
                }
            }

            $this->sortNestedArray($item);

            if (isset($item[self::ELEMENTS_IDENTIFIER])) {
                $elements = $item[self::ELEMENTS_IDENTIFIER];
                unset($item[self::ELEMENTS_IDENTIFIER]);
                $item[self::ELEMENTS_IDENTIFIER] = $elements;
            }
        }
    }
}
