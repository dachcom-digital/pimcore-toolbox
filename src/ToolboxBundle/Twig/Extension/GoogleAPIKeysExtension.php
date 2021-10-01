<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GoogleAPIKeysExtension extends AbstractExtension
{
    protected ?string $fallbackBrowserKey;
    protected ConfigManagerInterface $configManager;

    public function __construct(
        ?string $fallbackBrowserKey,
        ConfigManagerInterface $configManager
    ) {
        $this->fallbackBrowserKey = $fallbackBrowserKey;
        $this->configManager = $configManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_google_map_api_key', [$this, 'getGoogleMapAPIKey'])
        ];
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getGoogleMapAPIKey()
    {
        $browserKey = 'please_configure_key_in_systemsettings';
        $configNode = $this->configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('googleMap');

        if (!empty($configNode) && isset($configNode['map_api_key']) && !empty($configNode['map_api_key'])) {
            return $configNode['map_api_key'];
        }

        if (!empty($this->fallbackBrowserKey)) {
            return $this->fallbackBrowserKey;
        }

        return $browserKey;
    }
}
