<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model\Document;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;

class GoogleMap extends Document\Tag
{
    /**
     * @var bool
     */
    private $disableGoogleLookUp = false;

    /**
     * @var string
     */
    private $mapId;

    /**
     * Contains the data.
     *
     * @var array
     */
    public $data;

    /**
     * Return the type of the element.
     *
     * @return string
     */
    public function getType()
    {
        return 'googlemap';
    }

    /**
     * @see Document\Tag\TagInterface::getData
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return the data for direct output to the frontend, can also contain HTML code!
     *
     * @return string
     *
     * @throws \Exception
     */
    public function frontend()
    {
        $dataAttr = [];
        $dataAttr['data-locations'] = json_encode($this->data);
        $dataAttr['data-show-info-window-on-load'] = $this->options['iwOnInit'];

        $dataAttr['data-mapoption-zoom'] = $this->options['mapZoom'];
        $dataAttr['data-mapoption-map-type-id'] = $this->options['mapType'];

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

        $dataAttrString = implode(' ', array_map(
            function ($v, $k) {
                return $k . "='" . $v . "'";
            },
            $dataAttr,
            array_keys($dataAttr)
        ));

        if (empty($this->getId())) {
            $this->mapId = uniqid('map-');
        }

        $html = '<div class="embed-responsive-item toolbox-googlemap" id="' . $this->mapId . '" ' . $dataAttrString . '></div>';

        return $html;
    }

    /**
     * @see Document\Tag\TagInterface::admin
     *
     * @return mixed|string
     *
     * @throws \Exception
     */
    public function admin()
    {
        $html = parent::admin();

        // get frontendcode for preview
        // put the video code inside the generic code
        $html = str_replace('</div>', $this->frontend() . '</div>', $html);

        return $html;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->data === false || empty($this->data);
    }

    /**
     * Receives the data from the resource, an convert to the internal data in the object eg. image-id to Asset\Image.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function setDataFromResource($data)
    {
        $this->data = \Pimcore\Tool\Serialize::unserialize($data);
        if (!is_array($this->data)) {
            $this->data = [];
        }

        return $this;
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromEditmode
     *
     * @param mixed $data
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setDataFromEditmode($data)
    {
        if (!is_array($data)) {
            $data = [];
        }

        if (count($data) > 0) {
            foreach ($data as $i => $location) {
                $data[$i] = $this->geocodeLocation($location);
            }
        }

        $this->data = $data;

        return $this;
    }

    /**
     * @return bool
     */
    public function googleLookUpIsDisabled()
    {
        return $this->disableGoogleLookUp;
    }

    public function disableGoogleLookup()
    {
        $this->disableGoogleLookUp = true;
    }

    public function enableGoogleLookup()
    {
        $this->disableGoogleLookUp = false;
    }

    /**
     * @param string $mapId
     */
    public function setId($mapId)
    {
        $this->mapId = $mapId;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->mapId;
    }

    /**
     * @param array $location
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function geocodeLocation($location)
    {
        /** @var ConfigManager $configManager */
        $configManager = \Pimcore::getContainer()->get(ConfigManager::class);
        $configNode = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('googleMap');

        $address = $location['street'] . '+' . $location['zip'] . '+' . $location['city'] . '+' . $location['country'];
        $address = urlencode($address);

        $key = '';
        // first try to get server-api-key
        if (!empty($configNode) && isset($configNode['simple_api_key']) && !empty($configNode['simple_api_key'])) {
            $key = '&key=' . $configNode['simple_api_key'];
        }
        // if not set, get browser-api-key
        if ($key === '' && !empty($configNode) && isset($configNode['map_api_key']) && !empty($configNode['map_api_key'])) {
            $key = '&key=' . $configNode['map_api_key'];
        }

        if ($this->googleLookUpIsDisabled()) {
            return $location;
        }

        $url = 'https://maps.google.com/maps/api/geocode/json?address=' . $address . $key;

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
}
