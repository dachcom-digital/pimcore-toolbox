<?php

namespace ToolboxBundle\Manager;

use ToolboxBundle\Resolver\ContextResolverInterface;

class ConfigManager implements ConfigManagerInterface
{
    private ContextResolverInterface $contextResolver;
    private bool $contextResolved = false;
    protected array $config;
    protected array $context = [];
    protected array $contextSettings = [];
    protected ?string $currentContextId = null;

    public function __construct(ContextResolverInterface $contextResolver)
    {
        $this->contextResolver = $contextResolver;
    }

    public function setConfig(array $config = []): void
    {
        $this->config = $config;
    }

    public function addAdditionalAreaConfig(array $additionalAreaConfig = []): void
    {
        foreach ($additionalAreaConfig as $additionalAreaId) {

            if (array_key_exists($additionalAreaId, $this->config['areas'])) {
                continue;
            }

            $this->config['areas'][$additionalAreaId] = [
                'enabled' => true
            ];

        }
    }

    public function getConfig(string $section): mixed
    {
        $this->ensureCoreConfig();

        return $this->config[$section];
    }

    public function isContextConfig(): bool
    {
        $this->ensureCoreConfig();

        return $this->currentContextId !== null;
    }

    public function getCurrentContextSettings(): array
    {
        $this->ensureCoreConfig();

        if ($this->currentContextId === null) {
            return [];
        }

        return $this->contextSettings[$this->currentContextId];
    }

    public function areaIsEnabled(string $areaName): bool
    {
        $this->ensureCoreConfig();

        if (array_key_exists($areaName, $this->config['areas'])) {
            return $this->config['areas'][$areaName]['enabled'] === true;
        }

        return true;
    }

    public function getAreaConfig(string $areaName): mixed
    {
        $this->ensureCoreConfig();

        return $this->config['areas'][$areaName] ?? [];
    }

    public function getAreaElementConfig(string $areaName, string $configElementName): mixed
    {
        $this->ensureCoreConfig();

        return $this->config['areas'][$areaName]['config_elements'][$configElementName];
    }

    public function getAreaParameterConfig(string $areaName): mixed
    {
        $this->ensureCoreConfig();

        return $this->config['areas'][$areaName]['config_parameter'];
    }

    public function getImageThumbnailFromConfig(string $thumbnailName = ''): ?string
    {
        $this->ensureCoreConfig();

        return $this->config['image_thumbnails'][$thumbnailName] ?? null;
    }

    public function getContextIdentifier(): ?string
    {
        return $this->contextResolver->getCurrentContextIdentifier();
    }

    /**
     * @throws \Exception
     */
    private function ensureCoreConfig(): void
    {
        $contextIdentifierId = $this->getContextIdentifier();

        if ($contextIdentifierId === null) {
            $this->contextResolved = true;

            return;
        }

        if ($this->contextResolved === true) {
            return;
        }

        $contextData = $this->parseContextConfig($contextIdentifierId);
        $this->config = $contextData['config'];
        $this->contextSettings[$contextIdentifierId] = $contextData['settings'];
        $this->currentContextId = $contextIdentifierId;

        $this->contextResolved = true;
    }

    /**
     * @throws \Exception
     */
    private function parseContextConfig(string $currentContextId): array
    {
        if (!isset($this->config['context'][$currentContextId])) {
            throw new \Exception(sprintf(
                'toolbox context conflict: context with identifier "%s" is not configured.',
                $currentContextId
            ));
        }

        $contextData = $this->config['context'][$currentContextId];
        $contextSettings = $contextData['settings'];

        unset($contextData['settings']);

        if ($contextSettings['merge_with_root'] === true) {
            $parsedData = $contextData;

            // enabled areas passes first!
            if (!empty($contextSettings['enabled_areas'])) {
                $filteredElements = [];
                foreach ($contextSettings['enabled_areas'] as $areaId) {
                    if (isset($parsedData['areas'][$areaId])) {
                        $filteredElements[$areaId] = $parsedData['areas'][$areaId];
                    }
                }

                $parsedData['areas'] = $filteredElements;

                // remove disabled areas for this context
            } elseif (!empty($contextSettings['disabled_areas'])) {
                foreach ($contextSettings['disabled_areas'] as $areaId) {
                    if (isset($parsedData['areas'][$areaId])) {
                        unset($parsedData['areas'][$areaId]);
                    }
                }
            }
        } else {
            $parsedData = $contextData;
        }

        return [
            'config' => $parsedData,
            'settings' => $contextSettings
        ];
    }
}
