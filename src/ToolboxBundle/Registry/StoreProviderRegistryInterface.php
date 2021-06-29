<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Provider\StoreProviderInterface;

interface StoreProviderRegistryInterface
{
    public function register(string $identifier, StoreProviderInterface $service): void;

    public function has(string $identifier): bool;

    public function get(string $identifier): StoreProviderInterface;
}
