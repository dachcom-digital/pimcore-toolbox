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

namespace ToolboxBundle\Registry;

use ToolboxBundle\Normalizer\PropertyNormalizerInterface;

class NormalizerRegistry implements NormalizerRegistryInterface
{
    protected array $normalizer = [];

    public function register(string $normalizerName, mixed $service): void
    {
        if (!in_array(PropertyNormalizerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), PropertyNormalizerInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->normalizer[$normalizerName] = $service;
    }

    public function has(string $normalizerName): bool
    {
        return isset($this->normalizer[$normalizerName]);
    }

    public function get($normalizerName): PropertyNormalizerInterface
    {
        if (!$this->has($normalizerName)) {
            throw new \Exception('"' . $normalizerName . '" Normalizer does not exist');
        }

        return $this->normalizer[$normalizerName];
    }
}
