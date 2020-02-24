<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Provider\StoreProviderInterface;

class StoreProviderRegistry implements StoreProviderRegistryInterface
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * {@inheritDoc}
     */
    public function register($identifier, $service)
    {
        if (!in_array(StoreProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), StoreProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->services[$identifier] = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function has($identifier)
    {
        return isset($this->services[$identifier]);
    }

    /**
     * {@inheritDoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Store provider does not exist.');
        }

        return $this->services[$identifier];
    }
}
