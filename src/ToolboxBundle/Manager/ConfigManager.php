<?php

namespace ToolboxBundle\Manager;

use ToolboxBundle\Resolver\ContextResolverInterface;

class ConfigManager implements ConfigManagerInterface
{
    protected ContextResolverInterface $contextResolver;
    protected bool $contextResolved = false;
    protected array $config;
    protected array $context = [];
    protected array $contextSettings = [];
    protected ?string $currentContextId = null;
    protected ?string $areaNamespace = null;

    public function __construct(ContextResolverInterface $contextResolver)
    {
        $this->contextResolver = $contextResolver;
    }

    public function setConfig(array $config = []): void
    {
        $this->config = $config;
    }

    public function setAreaNameSpace(string $namespace = ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL): static
    {
        $this->areaNamespace = $namespace;

        return $this;
    }

    public function getConfig(string $section)
    {
        $this->ensureCoreConfig();

        return $this->config[$section];
    }

    public function isContextConfig(): bool
    {
        $this->ensureCoreConfig();

        return $this->currentContextId !== null;
    }

    public function getCurrentContextSettings(): ?array
    {
        $this->ensureCoreConfig();

        if ($this->currentContextId === null) {
            return null;
        }

        return $this->contextSettings[$this->currentContextId];
    }

    public function getAreaConfig(string $areaName = ''): array
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();

        return $this->config[$this->areaNamespace][$areaName];
    }

    public function getAreaElementConfig(string $areaName = '', string $configElementName = ''): array
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();

        return $this->config[$this->areaNamespace][$areaName]['config_elements'][$configElementName];
    }

    public function getAreaParameterConfig(string $areaName = ''): array
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();

        return $this->config[$this->areaNamespace][$areaName]['config_parameter'];
    }

    public function getImageThumbnailFromConfig(string $thumbnailName = ''): string
    {
        $this->ensureCoreConfig();

        return $this->config['image_thumbnails'][$thumbnailName];
    }

    public function getContextIdentifier(): ?string
    {
        return $this->contextResolver->getCurrentContextIdentifier();
    }

    private function ensureCoreConfig(): void
    {
        $contextIdentifierId = $this->getContextIdentifier();

        if ($contextIdentifierId === false) {
            $this->contextResolved = true;

            return;
        }

        if ($this->contextResolved === true) {
            return;
        }

        if ($contextIdentifierId !== null) {
            $contextData = $this->parseContextConfig($contextIdentifierId);
            $this->config = $contextData['config'];
            $this->contextSettings[$contextIdentifierId] = $contextData['settings'];
            $this->currentContextId = $contextIdentifierId;
        }

        $this->contextResolved = true;
    }

    private function ensureConfigNamespace(): void
    {
        if (is_null($this->areaNamespace)) {
            throw new \Exception('ConfigManager has no defined namespace.');
        }
    }

    private function parseContextConfig(string $currentContextId): array
    {
        if (!is_string($currentContextId) || !isset($this->config['context'][$currentContextId])) {
            @trigger_error(sprintf(
                'toolbox context conflict: context with identifier "%s" is not configured.',
                $currentContextId
            ), E_USER_ERROR);
        }

        $contextData = $this->config['context'][$currentContextId];
        $contextSettings = $contextData['settings'];

        unset($contextData['settings']);

        if ($contextSettings['merge_with_root'] === true) {
            $parsedData = $contextData;

            // enabled areas passes first!
            if (!empty($contextSettings['enabled_areas'])) {
                $filteredElements = ['areas' => [], 'custom_areas' => []];
                foreach ($contextSettings['enabled_areas'] as $areaId) {
                    if (isset($parsedData['areas'][$areaId])) {
                        $filteredElements['areas'][$areaId] = $parsedData['areas'][$areaId];
                    } elseif (isset($parsedData['custom_areas'][$areaId])) {
                        $filteredElements['areas'][$areaId] = $parsedData['custom_areas'][$areaId];
                    }
                }

                $parsedData['areas'] = $filteredElements['areas'];
                $parsedData['custom_areas'] = $filteredElements['custom_areas'];

            // remove disabled areas for this context
            } elseif (!empty($contextSettings['disabled_areas'])) {
                foreach ($contextSettings['disabled_areas'] as $areaId) {
                    if (isset($parsedData['areas'][$areaId])) {
                        unset($parsedData['areas'][$areaId]);
                    } elseif (isset($parsedData['custom_areas'][$areaId])) {
                        unset($parsedData['custom_areas'][$areaId]);
                    }
                }
            }
        } else {
            $parsedData = $contextData;
        }

        return ['config' => $parsedData, 'settings' => $contextSettings];
    }
}
