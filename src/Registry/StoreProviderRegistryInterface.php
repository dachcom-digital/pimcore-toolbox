<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Provider\StoreProviderInterface;

interface StoreProviderRegistryInterface
{
    public function register(string $identifier, mixed $service): void;

    public function has(string $identifier): bool;

    /**
     * @throws \Exception
     */
    public function get(string $identifier): StoreProviderInterface;
}
