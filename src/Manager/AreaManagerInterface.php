<?php

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
