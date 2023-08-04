<?php

namespace ToolboxBundle\Normalizer;

interface PropertyNormalizerInterface
{
    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed;
}
