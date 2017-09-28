<?php

namespace ToolboxBundle\Service;

use Symfony\Component\Finder\Finder;

class ConfigManager
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $areaNamespace = NULL;

    const AREABRICK_NAMESPACE_INTERNAL = 'areas';

    const AREABRICK_NAMESPACE_EXTERNAL = 'custom_areas';

    /**
     * @param array $config
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
     *
     * @return mixed
     */
    public function getConfig($section)
    {
        return $this->config[$section];
    }

    /**
     * @param string $areaName
     *
     * @return mixed
     */
    public function getAreaConfig($areaName = '')
    {
        $this->checkConfigNamespace();
        return $this->config[$this->areaNamespace][$areaName];
    }

    /**
     * @param string $areaName
     * @param string $configElementName
     *
     * @return mixed
     */
    public function getAreaElementConfig($areaName = '', $configElementName = '')
    {
        $this->checkConfigNamespace();
        return $this->config[$this->areaNamespace][$areaName]['config_elements'][$configElementName];
    }

    /**
     * @param string $areaName
     *
     * @return mixed
     */
    public function getAreaParameterConfig($areaName = '')
    {
        $this->checkConfigNamespace();
        return $this->config[$this->areaNamespace][$areaName]['config_parameter'];
    }

    /**
     * @param string $thumbnailName
     *
     * @return mixed
     */
    public function getImageThumbnailFromConfig($thumbnailName = '')
    {
        return $this->config['image_thumbnails'][$thumbnailName];
    }

    /**
     * @return array
     */
    public function getValidCoreBricks()
    {
        $areaBricks = [];
        $finder = new Finder();
        $finder->name('*.yml');

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->in(dirname(__DIR__) . '/Resources/config/pimcore/areas') as $file) {
            $areaBricks[] = str_replace('.' . $file->getExtension(), '', $file->getFilename());
        }

        return $areaBricks;
    }

    /**
     * @throws \Exception
     */
    private function checkConfigNamespace()
    {
        if (is_null($this->areaNamespace)) {
            throw new \Exception('configManger has no defined namespace.');
        }
    }

}