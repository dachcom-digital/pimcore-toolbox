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

namespace ToolboxBundle\Manager;

interface ConfigManagerInterface
{
    public function setConfig(array $config = []): void;

    /**
     * @throws \Exception
     */
    public function getConfig(string $section): mixed;

    /**
     * @throws \Exception
     */
    public function isContextConfig(): bool;

    /**
     * @throws \Exception
     */
    public function getCurrentContextSettings(): array;

    /**
     * @throws \Exception
     */
    public function getHeadlessDocumentConfig(string $headlessDocumentName): array;

    /**
     * @throws \Exception
     */
    public function areaIsEnabled(string $areaName): bool;

    /**
     * @throws \Exception
     */
    public function getAreaConfig(string $areaName): mixed;

    /**
     * @throws \Exception
     */
    public function getAreaElementConfig(string $areaName, string $configElementName): mixed;

    /**
     * @throws \Exception
     */
    public function getAreaParameterConfig(string $areaName): mixed;

    /**
     * @throws \Exception
     */
    public function getImageThumbnailFromConfig(string $thumbnailName): ?string;

    public function getContextIdentifier(): ?string;
}
