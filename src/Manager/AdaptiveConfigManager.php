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

class AdaptiveConfigManager extends ConfigManager implements AdaptiveConfigManagerInterface
{
    protected ?string $adaptiveContextId;

    public function setContextNameSpace(?string $id = null): void
    {
        $this->adaptiveContextId = $id;
    }

    public function getContextIdentifier(): ?string
    {
        return $this->adaptiveContextId;
    }
}
