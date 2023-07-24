<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GoogleAPIKeysExtension extends AbstractExtension
{
    public function __construct(
        protected ?string $fallbackBrowserKey,
        protected ConfigManagerInterface $configManager
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_google_map_api_key', [$this, 'getGoogleMapAPIKey'])
        ];
    }

    /**
     * @throws \Exception
     */
    public function getGoogleMapAPIKey(): mixed
    {
        $browserKey = 'please_configure_key_in_systemsettings';
        $configNode = $this->configManager->getAreaParameterConfig('googleMap');

        if (!empty($configNode) && !empty($configNode['map_api_key'])) {
            return $configNode['map_api_key'];
        }

        if (!empty($this->fallbackBrowserKey)) {
            return $this->fallbackBrowserKey;
        }

        return $browserKey;
    }
}
