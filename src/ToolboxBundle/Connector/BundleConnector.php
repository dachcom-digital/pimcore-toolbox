<?php

namespace ToolboxBundle\Connector;

use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class BundleConnector
{
    protected PimcoreBundleManager $bundleManager;
    protected array $services = [];

    public function __construct(PimcoreBundleManager $bundleManager)
    {
        $this->bundleManager = $bundleManager;
    }

    public function registerBundleService(string $serviceId, $service)
    {
        $this->services[$serviceId] = $service;
    }

    public function getBundleService(string $serviceId)
    {
        if (!isset($this->services[$serviceId])) {
            throw new ServiceNotFoundException(sprintf('BundleConnector Service "%s" not found', $serviceId));
        }

        return $this->services[$serviceId];
    }

    public function hasBundle(string $bundleName = 'ExtensionBundle\ExtensionBundle'): bool
    {
        try {
            $hasExtension = $this->bundleManager->isEnabled($bundleName);
        } catch (\Exception $e) {
            $hasExtension = false;
        }

        return $hasExtension;
    }
}
