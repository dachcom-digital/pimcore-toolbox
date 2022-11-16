<?php

namespace ToolboxBundle\Maintenance;

use Pimcore\Maintenance\TaskInterface;
use ToolboxBundle\Manager\PermissionManagerInterface;

class AreaPermissionTask implements TaskInterface
{
    protected PermissionManagerInterface $permissionManager;

    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    public function execute(): void
    {
        $this->permissionManager->synchroniseEditablePermissions();
    }
}
