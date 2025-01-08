<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
