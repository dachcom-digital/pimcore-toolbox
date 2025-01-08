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

namespace ToolboxBundle\Maintenance;

use Pimcore\Maintenance\TaskInterface;
use ToolboxBundle\Manager\PermissionManagerInterface;

class AreaPermissionTask implements TaskInterface
{
    public function __construct(protected PermissionManagerInterface $permissionManager)
    {
    }

    public function execute(): void
    {
        $this->permissionManager->synchroniseEditablePermissions();
    }
}
