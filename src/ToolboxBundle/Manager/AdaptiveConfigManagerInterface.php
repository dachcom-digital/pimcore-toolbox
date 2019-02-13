<?php

namespace ToolboxBundle\Manager;

interface AdaptiveConfigManagerInterface extends ConfigManagerInterface
{
    /**
     * @param string $id
     */
    public function setContextNameSpace($id);
}