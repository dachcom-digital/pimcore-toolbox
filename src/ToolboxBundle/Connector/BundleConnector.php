<?php

namespace ToolboxBundle\Connector;

use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class BundleConnector
{
    /**
     * @var PimcoreBundleManager
     */
    protected $bundleManager;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @param PimcoreBundleManager $bundleManager
     */
    public function __construct(PimcoreBundleManager $bundleManager)
    {
        $this->bundleManager = $bundleManager;
    }

    /**
     * @param string $serviceId
     * @param mixed  $service
     */
    public function registerBundleService($serviceId, $service)
    {
        $this->services[$serviceId] = $service;
    }

    /**
     * @param string $serviceId
     *
     * @return mixed
     */
    public function getBundleService($serviceId)
    {
        if (!isset($this->services[$serviceId])) {
            throw new ServiceNotFoundException(sprintf('BundleConnector Service "%s" not found', $serviceId));
        }

        return $this->services[$serviceId];
    }

    /**
     * @param string $bundleName
     *
     * @return bool
     */
    public function hasBundle($bundleName = 'ExtensionBundle\ExtensionBundle')
    {
        try {
            $hasExtension = $this->bundleManager->isEnabled($bundleName);
        } catch (\Exception $e) {
            $hasExtension = false;
        }

        return $hasExtension;
    }

}