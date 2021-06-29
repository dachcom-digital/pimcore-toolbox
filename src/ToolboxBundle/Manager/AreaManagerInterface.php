<?php

namespace ToolboxBundle\Manager;

interface AreaManagerInterface
{
    public function getAreaBlockName(string $type = null): string;

    public function getAreaBlockConfiguration(string $type = null, bool $fromSnippet = false): array;
}
