<?php

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model\Document;
use Pimcore\Tool\Serialize;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

class GoogleMap extends Document\Editable
{
    private bool $disableGoogleLookUp = false;
    private ?string $mapId = null;
    private array $data = [];

    public function getType(): string
    {
        return 'googlemap';
    }

    public function getDataEditmode(): array
    {
        $key = $this->detectKey();

        return [
            'locations'   => $this->getData(),
            'hasValidKey' => $key !== null,
            'id'          => $this->getId(),
            'attributes'  => $this->buildMapAttributes()
        ];
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function frontend(): string
    {
        $attributes = $this->buildMapAttributes();

        $dataAttrString = implode(' ', array_map(static function ($v, $k) {
            return sprintf('%s="%s"', $k, $v);
        },
            $attributes,
            array_keys($attributes)
        ));

        return '<div class="embed-responsive-item toolbox-googlemap" id="' . $this->getId() . '" ' . $dataAttrString . '></div>';
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function admin()
    {
        $html = parent::admin();

        // get frontend code for preview
        // put the video code inside the generic code
        return str_replace('</div>', $this->frontend() . '</div>', $html);
    }

    public function setDataFromResource(mixed $data): self
    {
        $this->setId(uniqid('map-', true));

        $parsedLocations = Serialize::unserialize($data);

        if (!is_array($parsedLocations)) {
            $parsedLocations = [];
        }

        $this->data = $parsedLocations;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function setDataFromEditmode(mixed $data): self
    {
        $this->setId(uniqid('map-', true));

        if (!is_array($data)) {
            $data = [];
        }

        $parsedLocations = [];
        $key = $this->detectKey();

        if (count($data) > 0) {
            foreach ($data as $i => $location) {
                $parsedLocations[$i] = $this->geocodeLocation($location, $key);
            }
        }

        $this->data = $parsedLocations;

        return $this;
    }

    public function googleLookUpIsDisabled(): bool
    {
        return $this->disableGoogleLookUp;
    }

    public function disableGoogleLookup(): void
    {
        $this->disableGoogleLookUp = true;
    }

    public function enableGoogleLookup(): void
    {
        $this->disableGoogleLookUp = false;
    }

    public function setId(string $mapId): void
    {
        if ($this->mapId !== null) {
            return;
        }

        $this->mapId = $mapId;
    }

    public function getId(): ?string
    {
        return $this->mapId;
    }

    protected function buildMapAttributes(): array
    {
        if (!is_array($this->config)) {
            $this->config = [];
        }

        $dataAttr = [];
        $dataAttr['data-locations'] = htmlspecialchars(json_encode($this->data), ENT_QUOTES, 'UTF-8');
        $dataAttr['data-show-info-window-on-load'] = $this->config['iwOnInit'] ?? true;

        $dataAttr['data-mapoption-zoom'] = $this->config['mapZoom'] ?? 5;
        $dataAttr['data-mapoption-map-type-id'] = $this->config['mapType'] ?? 'roadmap';

        /** @var ConfigManager $configManager */
        $configManager = \Pimcore::getContainer()->get(ConfigManager::class);
        $configNode = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('googleMap');

        if (!empty($configNode)) {
            $mapOptions = $configNode['map_options'];
            $mapStyleUrl = $configNode['map_style_url'];
            $markerIcon = $configNode['marker_icon'];

            if (is_array($mapOptions) && count($mapOptions) > 0) {
                foreach ($mapOptions as $name => $value) {
                    $value = is_bool($value) ? ($value === true ? 'true' : 'false') : (string) $value;
                    // convert camelCase to camel-case, because we will read these property with $el.data(), which converts them back to camelCase
                    $name = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '-\\1', $name));
                    $dataAttr['data-mapoption-' . $name] = $value;
                }
            }

            if (!empty($mapStyleUrl)) {
                $dataAttr['data-mapstyleurl'] = $mapStyleUrl;
            }

            if (!empty($markerIcon)) {
                $dataAttr['data-markericon'] = $markerIcon;
            }
        }

        return $dataAttr;
    }

    /**
     * @throws \Exception
     */
    protected function geocodeLocation(array $location, ?string $key): array
    {
        if ($key === null || $this->googleLookUpIsDisabled()) {
            return array_merge($location, ['lat' => null, 'lng' => null]);
        }

        $address = $location['street'] . '+' . $location['zip'] . '+' . $location['city'] . '+' . $location['country'];
        $address = urlencode($address);

        $keyParam = sprintf('&key=%s', $key);
        $url = sprintf('https://maps.google.com/maps/api/geocode/json?address=%s%s', $address, $keyParam);

        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $response = curl_exec($c);
        curl_close($c);

        $result = json_decode($response, false);

        if ($result->status === 'OK') {
            $location['lat'] = $result->results[0]->geometry->location->lat;
            $location['lng'] = $result->results[0]->geometry->location->lng;
        }

        return $location;
    }

    protected function detectKey(): ?string
    {
        /** @var ConfigManager $configManager */
        $configManager = \Pimcore::getContainer()->get(ConfigManager::class);
        $configNode = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('googleMap');

        $fallbackSimpleKey = \Pimcore::getContainer()->getParameter('toolbox.google_maps.simple_api_key');
        $fallbackBrowserKey = \Pimcore::getContainer()->getParameter('toolbox.google_maps.browser_api_key');

        // first try to get server-api-key
        if (!empty($configNode) && isset($configNode['simple_api_key']) && !empty($configNode['simple_api_key'])) {
            return $configNode['simple_api_key'];
        }

        if (!empty($fallbackSimpleKey)) {
            return $fallbackSimpleKey;
        }

        if (!empty($configNode) && isset($configNode['map_api_key']) && !empty($configNode['map_api_key'])) {
            return $configNode['map_api_key'];
        }

        if (!empty($fallbackBrowserKey)) {
            return $fallbackBrowserKey;
        }

        return null;
    }

    public function __sleep()
    {
        $parentVars = parent::__sleep();

        if (!in_array('data', $parentVars, true)) {
            $parentVars[] = 'data';
        }

        return $parentVars;
    }
}
