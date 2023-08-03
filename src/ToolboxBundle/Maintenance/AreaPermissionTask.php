<?php

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
