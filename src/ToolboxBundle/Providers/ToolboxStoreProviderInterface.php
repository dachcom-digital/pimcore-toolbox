<?php

namespace ToolboxBundle\Providers;

interface ToolboxStoreProviderInterface
{
    /**
     * for select, multiselect, simply return a key-value-array
     *
     * @return null|array|string
     */
    public function getValues();
}
