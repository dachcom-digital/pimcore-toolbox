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
use ToolboxBundle\Document\Editable\HeadlessEditableRenderer;
use ToolboxBundle\Factory\HeadlessEditableInfoFactory;

class InlineConfigBuilder extends AbstractConfigBuilder implements InlineConfigBuilderInterface
{
    protected HeadlessEditableRenderer $headlessEditableRenderer;
    protected HeadlessEditableInfoFactory $editableInfoFactory;

    public function setHeadlessEditableRenderer(HeadlessEditableRenderer $headlessEditableRenderer): void
    {
        $this->headlessEditableRenderer = $headlessEditableRenderer;
    }

    public function setHeadlessEditableInfoFactory(HeadlessEditableInfoFactory $editableInfoFactory): void
    {
        $this->editableInfoFactory = $editableInfoFactory;
    }

    public function buildInlineConfiguration(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $editMode = false): string
    {
        $configurationView = [];
        $inlineConfigElements = $areaConfig['inline_config_elements'] ?? [];

        $items = $this->parseConfigElements($info, $brickId, $themeOptions, $inlineConfigElements, [], false);

        foreach ($items as $item) {
            $headlessInfo = $this->editableInfoFactory->createViaBrick($info, $editMode, $item);
            $renderedEditable = $this->headlessEditableRenderer->buildEditable($headlessInfo);

            $configurationView[] = $this->headlessEditableRenderer->renderEditableWithWrapper($item['type'], [
                'item'     => $item,
                'editable' => $renderedEditable
            ]);
        }

        if (count($configurationView) === 0) {
            return '';
        }

        return $this->headlessEditableRenderer->renderBrickWithWrapper($configurationView);
    }

    public function buildInlineConfigurationData(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = []): array
    {
        $data = [];
        $inlineConfigElements = $areaConfig['inline_config_elements'] ?? [];

        foreach ($inlineConfigElements as $itemName => $item) {
            $item['name'] = $itemName;

            $headlessInfo = $this->editableInfoFactory->createViaBrick($info, false, $item);

            $data[$itemName] = $this->headlessEditableRenderer->buildEditable($headlessInfo);
        }

        return $data;
    }
}
