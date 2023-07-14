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
    private ?string $areaNamespace = null;

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

            if (array_key_exists($additionalAreaId, $this->config[ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL])) {
                continue;
            }

            $this->config[ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL][$additionalAreaId] = [
                'enabled' => true
            ];

        }
    }

    public function setAreaNameSpace(string $namespace = ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL): self
    {
        $this->areaNamespace = $namespace;

        return $this;
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
        $this->ensureConfigNamespace();

        if (array_key_exists($areaName, $this->config[ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL])) {
            return $this->config[ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL][$areaName]['enabled'] === true;
        }

        if (array_key_exists($areaName, $this->config[ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL])) {
            return $this->config[ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL][$areaName]['enabled'] === true;
        }

        return true;
    }

    public function getAreaConfig(string $areaName): mixed
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();

        return $this->config[$this->areaNamespace][$areaName] ?? [];
    }

    public function getAreaElementConfig(string $areaName, string $configElementName): mixed
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();

        return $this->config[$this->areaNamespace][$areaName]['config_elements'][$configElementName];
    }

    public function getAreaParameterConfig(string $areaName): mixed
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();

        return $this->config[$this->areaNamespace][$areaName]['config_parameter'];
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
    private function ensureConfigNamespace(): void
    {
        if (is_null($this->areaNamespace)) {
            throw new \Exception('ConfigManager has no defined namespace.');
        }
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
