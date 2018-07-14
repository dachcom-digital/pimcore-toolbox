<?php

namespace ToolboxBundle\Manager;

interface ConfigManagerInterface
{
    const AREABRICK_NAMESPACE_INTERNAL = 'areas';

    const AREABRICK_NAMESPACE_EXTERNAL = 'custom_areas';

    /**
     * @param array $config
     * @throws \Exception
     */
    public function setConfig($config = []);

    /**
     * @param string $namespace
     *
     * @return $this
     */
    public function setAreaNameSpace($namespace = self::AREABRICK_NAMESPACE_INTERNAL);

    /**
     * @param $section
     * @return mixed
     * @throws \Exception
     */
    public function getConfig($section);

    /**
     * @throws \Exception
     * @return bool
     */
    public function isContextConfig();

    /**
     * @return false|array
     * @throws \Exception
     */
    public function getCurrentContextSettings();

    /**
     * @param string $areaName
     * @return mixed
     * @throws \Exception
     */
    public function getAreaConfig($areaName = '');

    /**
     * @param string $areaName
     * @param string $configElementName
     * @return mixed
     * @throws \Exception
     */
    public function getAreaElementConfig($areaName = '', $configElementName = '');

    /**
     * @param string $areaName
     * @return mixed
     * @throws \Exception
     */
    public function getAreaParameterConfig($areaName = '');

    /**
     * @param string $thumbnailName
     * @return mixed
     * @throws \Exception
     */
    public function getImageThumbnailFromConfig($thumbnailName = '');

    /**
     * @return string|null|false
     */
    public function getContextIdentifier();
}