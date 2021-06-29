<?php

namespace ToolboxBundle\Manager;

class AdaptiveConfigManager extends ConfigManager implements AdaptiveConfigManagerInterface
{
    protected ?string $adaptiveContextId = null;

    public function setContextNameSpace(?string $id): void
    {
        if (empty($id)) {
            $id = null;
        }

        $this->adaptiveContextId = $id;
    }

    public function getContextIdentifier(): ?String
    {
        return $this->adaptiveContextId;
    }
}
