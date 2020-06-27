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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return isset($this->services[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Store provider does not exist.');
        }

        return $this->services[$identifier];
    }
}
