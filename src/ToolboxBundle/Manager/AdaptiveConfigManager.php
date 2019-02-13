<?php

namespace ToolboxBundle\Manager;

class AdaptiveConfigManager extends ConfigManager implements AdaptiveConfigManagerInterface
{
    /**
     * @var null
     */
    protected $adaptiveContextId = null;

    /**
     * {@inheritdoc}
     */
    public function setContextNameSpace($id)
    {
        if (empty($id)) {
            $id = false;
        }

        $this->adaptiveContextId = $id;
    }

    /**
     * @return string|null
     */
    public function getContextIdentifier()
    {
        return $this->adaptiveContextId;
    }
}
