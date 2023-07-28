<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class EditableJsonSubscriber implements EventSubscriberInterface
{
    protected array $jsonEditables = [];

    public static function getSubscribedEvents(): array
    {
        return [
            EditableWorker::EDITABLE_JSON_RESPONSE => 'onEditableResponse',
        ];
    }

    public function onEditableResponse(GenericEvent $event): void
    {
        $name = $event->getArgument('name');
        $brickId = $event->getArgument('brickId');
        $indexes = $event->getArgument('indexes');
        $blocks = $event->getArgument('blocks');
        $isSimpleEditable = $event->getArgument('isSimpleEditable');

        $hierarchicalName = $isSimpleEditable
            ? str_replace('.', ':', $name)
            : $this->buildHierarchicalName($blocks, $indexes);

        $this->jsonEditables[$hierarchicalName] = [$brickId, $event->getSubject()];
    }

    private function buildHierarchicalName(array $blocks, array $indexes): string
    {
        $parts = [];
        for ($i = 0, $iMax = count($blocks); $i < $iMax; $i++) {
            $part = $blocks[$i]->getRealName();
            if (isset($indexes[$i])) {
                $part = sprintf('%s:%d', $part, $indexes[$i]);
            }

            $parts[] = $part;
        }

        return implode(':', $parts);
    }

    private function convertNestedArray($flatArray): array
    {
        $nestedArray = [];

        foreach ($flatArray as $key => $value) {

            $keys = explode(':', $key);
            $currentArray = &$nestedArray;

            foreach ($keys as $nestedKey) {
                /** @phpstan-ignore-next-line */
                if (!isset($currentArray['elements'][$nestedKey])) {
                    $currentArray['elements'][$nestedKey] = [];
                }

                $currentArray = &$currentArray['elements'][$nestedKey];
            }

            $currentArray['name'] = $value[0];
            $currentArray['data'] = $value[1];
        }

        /** @phpstan-ignore-next-line */
        return $nestedArray['elements'] ?? [];
    }

    private function simplifyNestedArray(&$array): void
    {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as &$item) {

            if(!is_array($item)) {
                continue;
            }

            if (isset($item['elements']) && array_is_list($item['elements']) && count($item['elements']) === 1) {
                $item['elements'] = $item['elements'][0]['elements'] ?? $item['elements'][0];
            }

            $this->simplifyNestedArray($item);
        }
    }

    private function sortNestedArray(&$array): void
    {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as &$item) {

            if(!is_array($item)) {
                continue;
            }

            if (isset($item['elements']) && is_array($item['elements'])) {
                $item['elements'] = array_values($item['elements']);
            }

            $this->sortNestedArray($item);

            if (isset($item['elements'])) {
                $elements = $item['elements'];
                unset($item['elements']);
                $item['elements'] = $elements;
            }
        }
    }

    public function getJsonEditables(): array
    {
        $convertedEditables = $this->convertNestedArray($this->jsonEditables);

        $this->sortNestedArray($convertedEditables);
        $this->simplifyNestedArray($convertedEditables);

        return $convertedEditables;
    }

}
