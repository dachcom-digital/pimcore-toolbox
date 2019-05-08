<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;

class GoogleAPIKeysExtension extends \Twig_Extension
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * GoogleAPIKeysExtension constructor.
     *
     * @param ConfigManagerInterface $configManager
     */
    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('toolbox_google_map_api_key', [$this, 'getGoogleMapAPIKey'])
        ];
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getGoogleMapAPIKey()
    {
        /** @var ConfigManagerInterface $configManager */
        $configNode = $this->configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('googleMap');

        $browserKey = 'please_configure_key_in_systemsettings';
        if (!empty($configNode) && isset($configNode['map_api_key']) && !empty($configNode['map_api_key'])) {
            $browserKey = $configNode['map_api_key'];
        }

        return $browserKey;
    }

}
