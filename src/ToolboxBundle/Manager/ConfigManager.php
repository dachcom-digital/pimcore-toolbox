<?php

namespace ToolboxBundle\Manager;

use ToolboxBundle\Resolver\ContextResolverInterface;

class ConfigManager
{
    /**
     * @var ContextResolverInterface
     */
    private $contextResolver;

    /**
     * @var bool
     */
    private $contextResolved = false;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $context = [];

    /**
     * @var array
     */
    protected $contextSettings = [];

    /**
     * @var null|string
     */
    protected $currentContextId = null;

    /**
     * @var string
     */
    private $areaNamespace = null;

    const AREABRICK_NAMESPACE_INTERNAL = 'areas';

    const AREABRICK_NAMESPACE_EXTERNAL = 'custom_areas';

    /**
     * ConfigManager constructor.
     *
     * @param ContextResolverInterface $contextResolver
     */
    public function __construct(ContextResolverInterface $contextResolver)
    {
        $this->contextResolver = $contextResolver;
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    public function setConfig($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $namespace
     *
     * @return $this
     */
    public function setAreaNameSpace($namespace = self::AREABRICK_NAMESPACE_INTERNAL)
    {
        $this->areaNamespace = $namespace;
        return $this;
    }

    /**
     * @param $section
     * @return mixed
     * @throws \Exception
     */
    public function getConfig($section)
    {
        $this->ensureCoreConfig();
        return $this->config[$section];
    }

    /**
     * @throws \Exception
     * @return bool
     */
    public function isContextConfig()
    {
        $this->ensureCoreConfig();
        return $this->currentContextId != null;
    }

    /**
     * @return false|array
     * @throws \Exception
     */
    public function getCurrentContextSettings()
    {
        $this->ensureCoreConfig();

        if ($this->currentContextId === null) {
            return false;
        }

        return $this->contextSettings[$this->currentContextId];
    }

    /**
     * @param string $areaName
     * @return mixed
     * @throws \Exception
     */
    public function getAreaConfig($areaName = '')
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();
        return $this->config[$this->areaNamespace][$areaName];
    }

    /**
     * @param string $areaName
     * @param string $configElementName
     * @return mixed
     * @throws \Exception
     */
    public function getAreaElementConfig($areaName = '', $configElementName = '')
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();
        return $this->config[$this->areaNamespace][$areaName]['config_elements'][$configElementName];
    }

    /**
     * @param string $areaName
     * @return mixed
     * @throws \Exception
     */
    public function getAreaParameterConfig($areaName = '')
    {
        $this->ensureCoreConfig();
        $this->ensureConfigNamespace();
        return $this->config[$this->areaNamespace][$areaName]['config_parameter'];
    }

    /**
     * @param string $thumbnailName
     * @return mixed
     * @throws \Exception
     */
    public function getImageThumbnailFromConfig($thumbnailName = '')
    {
        $this->ensureCoreConfig();
        return $this->config['image_thumbnails'][$thumbnailName];
    }

    /**
     * @return string|null|false
     */
    public function getContextIdentifier()
    {
        return $this->contextResolver->getCurrentContextIdentifier();
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function ensureCoreConfig()
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

    /**
     * @throws \Exception
     */
    private function ensureConfigNamespace()
    {
        if (is_null($this->areaNamespace)) {
            throw new \Exception('ConfigManager has no defined namespace.');
        }
    }

    /**
     * @param $currentContextId
     * @return array
     * @throws \Exception
     */
    private function parseContextConfig($currentContextId)
    {
        if (!is_string($currentContextId) || !isset($this->config['context'][$currentContextId])) {
            @trigger_error(sprintf('toolbox context conflict: context with identifier "%s" is not configured.',
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