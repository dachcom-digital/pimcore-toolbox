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
    private $services = [];

    /**
     * Configuration constructor.
     *
     * @param PimcoreBundleManager $bundleManager
     */
    public function __construct(PimcoreBundleManager $bundleManager)
    {
        $this->bundleManager = $bundleManager;
    }

    /**
     * @param $serviceId
     * @param $service
     */
    public function registerBundleService($serviceId, $service)
    {
        $this->services[$serviceId] = $service;
    }

    /**
     * @param $serviceId
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
            $hasExtension = FALSE;
        }

        return $hasExtension;
    }

}