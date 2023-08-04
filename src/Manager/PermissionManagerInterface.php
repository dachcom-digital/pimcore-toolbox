<?php

namespace ToolboxBundle\Manager;

interface PermissionManagerInterface
{
    public function synchroniseEditablePermissions(): void;

    public function getDisallowedEditables(array $editables): array;
}
