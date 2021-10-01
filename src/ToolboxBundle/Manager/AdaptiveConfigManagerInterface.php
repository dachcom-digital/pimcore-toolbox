<?php

namespace ToolboxBundle\Manager;

interface AdaptiveConfigManagerInterface extends ConfigManagerInterface
{
    public function setContextNameSpace(?string $id = null): void;
}
