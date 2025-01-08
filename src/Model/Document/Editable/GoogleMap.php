<?php

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model\Document;
use Pimcore\Tool\Serialize;
use ToolboxBundle\Manager\ConfigManager;

class GoogleMap extends Document\Editable implements Document\Editable\EditmodeDataInterface
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

        $dataAttrString = implode(' ', array_map(
            static function ($v, $k) {
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

    public function admin()
    {
        $html = parent::admin();

        // get frontend code for preview
        // put the video code inside the generic code
        return str_replace('</div>', $this->frontend() . '</div>', $html);
    }

    public function setDataFromResource(mixed $data): static
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
    public function setDataFromEditmode(mixed $data): static
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
        $dataAttr = [];
        $dataAttr['data-locations'] = htmlspecialchars(json_encode($this->validateLocationValues($this->data)), ENT_QUOTES, 'UTF-8');
        $dataAttr['data-show-info-window-on-load'] = $this->config['iwOnInit'] ?? true;

        $dataAttr['data-mapoption-zoom'] = $this->config['mapZoom'] ?? 5;
        $dataAttr['data-mapoption-map-type-id'] = $this->config['mapType'] ?? 'roadmap';

        /** @var ConfigManager $configManager */
        $configManager = \Pimcore::getContainer()->get(ConfigManager::class);
        $configNode = $configManager->getAreaParameterConfig('googleMap');

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

    protected function geocodeLocation(array $location, ?string $key): array
    {
        if ($key === null || $this->googleLookUpIsDisabled()) {
            return array_merge($location, ['lat' => null, 'lng' => null, 'status' => 'Look-Up has been disabled or Google API Key is empty.']);
        }

        $address = $location['street'] . '+' . $location['zip'] . '+' . $location['city'] . '+' . $location['country'];
        $address = urlencode($address);

        $checksum = $location['checksum'] ?? null;
        $newChecksum = md5($address);

        // we don't need to call api again, nothing have been changed
        if ($checksum === $newChecksum) {
            return $location;
        }

        $keyParam = sprintf('&key=%s', $key);
        $url = sprintf('https://maps.google.com/maps/api/geocode/json?address=%s%s', $address, $keyParam);

        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $response = curl_exec($c);
        curl_close($c);

        try {
            $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return array_merge($location, ['lat' => null, 'lng' => null, 'status' => $e->getMessage()]);
        }

        $location['status'] = null;

        if ($result['status'] === 'OK') {
            $responseLocation = $result['results'][0]['geometry']['location'];
            $location['lat'] = $responseLocation['lat'];
            $location['lng'] = $responseLocation['lng'];
            $location['checksum'] = md5($address);
        } else {
            $location['status'] = $result['error_message'] ?? $result['status'];
        }

        return $location;
    }

    protected function detectKey(): ?string
    {
        /** @var ConfigManager $configManager */
        $configManager = \Pimcore::getContainer()->get(ConfigManager::class);
        $configNode = $configManager->getAreaParameterConfig('googleMap');

        $fallbackSimpleKey = \Pimcore::getContainer()->getParameter('toolbox.google_maps.simple_api_key');
        $fallbackBrowserKey = \Pimcore::getContainer()->getParameter('toolbox.google_maps.browser_api_key');

        // first try to get server-api-key
        if (!empty($configNode) && !empty($configNode['simple_api_key'])) {
            return $configNode['simple_api_key'];
        }

        if (!empty($fallbackSimpleKey)) {
            return $fallbackSimpleKey;
        }

        if (!empty($configNode) && !empty($configNode['map_api_key'])) {
            return $configNode['map_api_key'];
        }

        if (!empty($fallbackBrowserKey)) {
            return $fallbackBrowserKey;
        }

        return null;
    }

    protected function validateLocationValues(array $locations): array
    {
        $forbiddenFields = [
            'status',
            'checksum'
        ];

        $filteredLocations = [];
        foreach ($locations as $location) {
            $newLocation = array_filter($location, static function ($data) use ($forbiddenFields) {
                return !in_array($data, $forbiddenFields, true);
            }, ARRAY_FILTER_USE_KEY);

            if (isset($newLocation['lat']) && is_numeric($newLocation['lat'])) {
                $newLocation['lat'] = (float) $newLocation['lat'];
            }
            if (isset($newLocation['lng']) && is_numeric($newLocation['lng'])) {
                $newLocation['lng'] = (float) $newLocation['lng'];
            }

            $filteredLocations[] = $newLocation;
        }

        return $filteredLocations;
    }

    public function __sleep(): array
    {
        $parentVars = parent::__sleep();

        if (!in_array('data', $parentVars, true)) {
            $parentVars[] = 'data';
        }

        return $parentVars;
    }
}
