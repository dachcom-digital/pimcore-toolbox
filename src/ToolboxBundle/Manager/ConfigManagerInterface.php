<?php

namespace ToolboxBundle\Manager;

interface ConfigManagerInterface
{
    public const AREABRICK_NAMESPACE_INTERNAL = 'areas';

    public const AREABRICK_NAMESPACE_EXTERNAL = 'custom_areas';

    public function setConfig(array $config = []): void;

    public function setAreaNameSpace(string $namespace = self::AREABRICK_NAMESPACE_INTERNAL): static;

    public function getConfig(string $section);

    public function isContextConfig(): bool;

    public function getCurrentContextSettings(): ?array;

    public function getAreaConfig(string $areaName = ''): array;

    public function getAreaElementConfig(string $areaName = '', string $configElementName = ''): array;

    public function getAreaParameterConfig(string $areaName = ''): array;

    public function getImageThumbnailFromConfig(string $thumbnailName = ''): string;

    public function getContextIdentifier(): ?string;
}
