<?php

namespace ToolboxBundle\Manager;

interface AreaManagerInterface
{
    public function getAreaBlockName(?string $type = null): string;

    /**
     * @throws \Exception
     */
    public function getAreaBlockConfiguration(?string $type, bool $fromSnippet = false): array;
}
