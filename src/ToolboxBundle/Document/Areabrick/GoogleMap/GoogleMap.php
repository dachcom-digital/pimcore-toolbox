<?php

namespace ToolboxBundle\Document\Areabrick\GoogleMap;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class GoogleMap extends AbstractAreabrick
{
    /**
     * string
     */
    protected $googleMapsHostUrl;

    /**
     * GoogleMap constructor.
     *
     * @param string $googleMapsHostUrl
     */
    public function __construct($googleMapsHostUrl = '')
    {
        $this->googleMapsHostUrl = $googleMapsHostUrl;
    }

    /**
     * @param Info $info
     */
    public function action(Info $info)
    {
        parent::action($info);

        $info->getView()->googleMapsHostUrl = $this->googleMapsHostUrl;
    }

    public function getViewTemplate()
    {
        return 'ToolboxBundle:Areas/googleMap:view.' . $this->getTemplateSuffix();
    }

    public function getName()
    {
        return 'Google Map';
    }

    public function getDescription()
    {
        return 'Toolbox Google Map';
    }
}