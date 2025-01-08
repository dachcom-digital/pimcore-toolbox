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

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Templating\Renderer\EditableRenderer;
use Symfony\Contracts\Translation\TranslatorInterface;
use ToolboxBundle\Document\Editable\ConfigParser;
use ToolboxBundle\Manager\AreaManagerInterface;
use Twig\Environment;

abstract class AbstractConfigBuilder
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected Environment $templating,
        protected AreaManagerInterface $areaManager,
        protected ConfigParser $configParser,
        protected EditableRenderer $editableRenderer
    ) {
    }

    protected function parseConfigElements(
        ?Info $info,
        string $brickId,
        array $themeOptions,
        array $configElements,
        array $tabs,
        bool $allowTabs = true,
        bool $isInlineContext = false
    ): array {
        $editableNodes = [];

        if (empty($configElements)) {
            return $editableNodes;
        }

        $acStoreProcessed = false;

        foreach ($configElements as $configElementName => $elementData) {
            // skip inline rendered config elements
            if ($isInlineContext === true && array_key_exists('inline_rendered', $elementData) && $elementData['inline_rendered'] === true) {
                continue;
            }

            $editableNode = $this->parseConfigElement($info, $configElementName, $elementData, $acStoreProcessed, $brickId, $themeOptions);

            if ($editableNode === null) {
                continue;
            }

            $editableNodes[] = $editableNode;

            $editableNodes = $this->checkColumnAdjusterField($brickId, $elementData['tab'], $themeOptions, $configElementName, $editableNodes);

            if ($elementData['type'] === 'additionalClasses') {
                $acStoreProcessed = true;
            }
        }

        // move additional classes to bottom
        $defaultFields = [];
        $acFields = [];

        foreach ($editableNodes as $editableNode) {
            if ($editableNode['additional_classes_element'] === true) {
                $acFields[] = $editableNode;
            } else {
                $defaultFields[] = $editableNode;
            }
        }

        $editableNodes = array_merge($defaultFields, $acFields);

        // assign tabs, if configured
        if ($allowTabs === true && count($tabs) > 0) {
            $tabbedEditableNodes = [];
            foreach ($tabs as $tabId => $tabName) {
                $tabbedEditableNodes[] = [
                    'type'  => 'panel',
                    'title' => $this->translator->trans($tabName, [], 'admin'),
                    'items' => array_values(
                        array_filter($editableNodes, static function ($editableNode) use ($tabId) {
                            return $editableNode['tab'] === $tabId;
                        })
                    )
                ];
            }

            return [
                'type'  => 'tabpanel',
                'items' => $tabbedEditableNodes
            ];
        }

        return $editableNodes;
    }

    private function parseConfigElement(?Info $info, string $elementName, array $elementData, bool $acStoreProcessed, string $brickId, array $themeOptions): ?array
    {
        $editableType = $elementData['type'];

        $parsedNode = $this->configParser->parseConfigElement($info, $elementName, $elementData, $acStoreProcessed);

        if ($parsedNode === null) {
            return null;
        }

        if ($editableType === 'block' && array_key_exists('children', $elementData) && is_array($elementData['children']) && count($elementData['children']) > 0) {
            $parsedNode['children'] = $this->parseConfigElements($info, $brickId, $themeOptions, $elementData['children'], []);
        }

        return $parsedNode;
    }

    private function checkColumnAdjusterField(string $brickId, ?string $tab, array $themeOptions, string $configElementName, array $editableNodes): array
    {
        if ($brickId !== 'columns') {
            return $editableNodes;
        }

        if ($configElementName !== 'type') {
            return $editableNodes;
        }

        if (empty($themeOptions['grid']['breakpoints'])) {
            return $editableNodes;
        }

        $editableNodes[] = [
            'type'                       => 'columnadjuster',
            'name'                       => 'columnadjuster',
            'tab'                        => $tab,
            'label'                      => $this->translator->trans('Column adjuster', [], 'admin'),
            'config'                     => [],
            'additional_classes_element' => false,
        ];

        return $editableNodes;
    }
}
