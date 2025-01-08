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

interface AreaManagerInterface
{
    public const BRICK_GROUP_SORTING_ALPHABETICALLY = 'alphabetically';
    public const BRICK_GROUP_SORTING_MANUALLY = 'manually';

    public function getAreaBlockName(?string $type = null): string;

    /**
     * @throws \Exception
     */
    public function getAreaBlockConfiguration(?string $type, bool $fromSnippet = false, bool $editMode = false): array;
}
