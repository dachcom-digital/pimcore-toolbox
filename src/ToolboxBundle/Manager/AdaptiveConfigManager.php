<?php

namespace ToolboxBundle\Manager;

class AdaptiveConfigManager extends ConfigManager
{
    /**
     * @var null
     */
    protected $adaptiveContextId = null;

    /**
     * @param $id
     */
    public function setContextNameSpace($id)
    {
        if (empty($id)) {
            $id = false;
        }

        $this->adaptiveContextId = $id;
    }

    /**
     * @return string|null|false
     */
    public function getContextIdentifier()
    {
        return $this->adaptiveContextId;
    }
}