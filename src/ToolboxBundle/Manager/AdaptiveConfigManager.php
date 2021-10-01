<?php

namespace ToolboxBundle\Manager;

class AdaptiveConfigManager extends ConfigManager implements AdaptiveConfigManagerInterface
{
    protected ?string $adaptiveContextId;

    public function setContextNameSpace(?string $id = null): void
    {
        $this->adaptiveContextId = $id;
    }

    public function getContextIdentifier(): ?string
    {
        return $this->adaptiveContextId;
    }
}
