<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Provider\StoreProviderInterface;

interface StoreProviderRegistryInterface
{
    /**
     * @param string                 $identifier
     * @param StoreProviderInterface $service
     */
    public function register($identifier, $service);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * @param string $identifier
     *
     * @return StoreProviderInterface
     *
     * @throws \Exception
     */
    public function get($identifier);
}
