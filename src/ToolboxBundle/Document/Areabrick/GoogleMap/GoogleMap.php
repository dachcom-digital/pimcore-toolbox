<?php

namespace ToolboxBundle\Document\Areabrick\GoogleMap;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class GoogleMap extends AbstractAreabrick
{
    protected string $googleMapsHostUrl;

    public function __construct(string $googleMapsHostUrl = '')
    {
        $this->googleMapsHostUrl = $googleMapsHostUrl;
    }

    public function action(Info $info): ?Response
    {
        parent::action($info);

        $info->setParam('googleMapsHostUrl', $this->googleMapsHostUrl);

        return null;
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
