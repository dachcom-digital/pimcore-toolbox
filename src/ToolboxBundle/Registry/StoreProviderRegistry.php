<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Provider\StoreProviderInterface;

class StoreProviderRegistry implements StoreProviderRegistryInterface
{
    protected array $services = [];

    public function register(string $identifier, StoreProviderInterface $service): void
    {
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
