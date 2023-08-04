<?php

namespace ToolboxBundle\Connector;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class BundleConnector
{
    protected array $activeBundles = [];
    protected array $services = [];

    public function addActiveBundle(string $bundleName): void
    {
        $this->activeBundles[$bundleName] = true;
    }

    public function registerBundleService(string $serviceId, mixed $service): void
    {
        $this->services[$serviceId] = $service;
    }

    public function getBundleService(string $serviceId): mixed
    {
        if (!isset($this->services[$serviceId])) {
            throw new ServiceNotFoundException(sprintf('BundleConnector Service "%s" not found', $serviceId));
        }

        return $this->services[$serviceId];
    }

    public function hasBundle(string $bundleName): bool
    {
        return array_key_exists($bundleName, $this->activeBundles);
    }
}
