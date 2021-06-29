<?php

namespace ToolboxBundle\Document\Areabrick\GoogleMap;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class GoogleMap extends AbstractAreabrick
{
    protected ?string $googleMapsHostUrl;

    public function __construct(?string $googleMapsHostUrl = null)
    {
        $this->googleMapsHostUrl = $googleMapsHostUrl;
    }

    public function action(Info $info)
    {
        parent::action($info);

        $info->setParam('googleMapsHostUrl', $this->googleMapsHostUrl);
    }

    public function getName(): string
    {
        return 'Google Map';
    }

    public function getDescription(): string
    {
        return 'Toolbox Google Map';
    }
}
