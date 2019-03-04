<?php

namespace ToolboxBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

class GoogleAPIKeysExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
        $configManager = \Pimcore::getContainer()->get(ConfigManager::class);
        $configNode = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('googleMap');

        $browser_key = 'please_configure_key_in_systemsettings.';
        if (!empty($configNode) && isset($configNode['map_api_key']) && !empty($configNode['map_api_key'])) {
            $browser_key = $configNode['map_api_key'];
        }

        return $browser_key;
    }

}