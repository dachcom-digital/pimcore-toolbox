<?php

namespace ToolboxBundle\Manager;

interface AreaManagerInterface
{
    public function getAreaBlockName($type = null);

    /**
     * @param null $type
     * @param bool $fromSnippet
     * @return array
     * @throws \Exception
     */
    public function getAreaBlockConfiguration($type = null, $fromSnippet = false);
}