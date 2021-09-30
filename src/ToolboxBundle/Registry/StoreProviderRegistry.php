<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Provider\StoreProviderInterface;

class StoreProviderRegistry implements StoreProviderRegistryInterface
{
    protected array $services = [];

    public function register(string $identifier, string $service): void
    {
        if (!in_array(StoreProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), StoreProviderInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->services[$identifier] = $service;
    }

    public function has(string $identifier): bool
    {
        return isset($this->services[$identifier]);
    }

    public function get(string $identifier): StoreProviderInterface
    {
        if (!$this->has($identifier)) {
            throw new \Exception('"' . $identifier . '" Store provider does not exist.');
        }

        return $this->services[$identifier];
    }
}
