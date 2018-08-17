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
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $info->getView()->getParameters()->add(['googleMapsHostUrl' => $this->googleMapsHostUrl]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Google Map';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Google Map';
    }
}
