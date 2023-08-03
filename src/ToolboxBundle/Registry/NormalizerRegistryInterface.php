<?php

namespace ToolboxBundle\Registry;

use ToolboxBundle\Normalizer\PropertyNormalizerInterface;

interface NormalizerRegistryInterface
{
    public function register(string $normalizerName, mixed $service): void;

    public function has(string $normalizerName): bool;

    /**
     * @throws \Exception
     */
    public function get(string $normalizerName): PropertyNormalizerInterface;
}
