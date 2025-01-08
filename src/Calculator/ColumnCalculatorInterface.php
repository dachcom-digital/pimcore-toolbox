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

namespace ToolboxBundle\Calculator;

use ToolboxBundle\Manager\ConfigManagerInterface;

interface ColumnCalculatorInterface
{
    public function setConfigManager(ConfigManagerInterface $configManager): self;

    /**
     * @throws \Exception
     */
    public function calculateColumns(?string $value, ?array $customColumnConfiguration = null): array;

    /**
     * @throws \Exception
     */
    public function getColumnInfoForAdjuster(?string $value, ?array $customColumnConfiguration = null): bool|array;
}
