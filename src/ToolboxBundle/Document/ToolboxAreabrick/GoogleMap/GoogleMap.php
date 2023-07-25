<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\GoogleMap;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

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

    public function getTemplateDirectoryName(): string
    {
        return 'google_map';
    }

    public function getTemplate(): string
    {
        return sprintf('@Toolbox/areas/%s/view.%s', $this->getTemplateDirectoryName(), $this->getTemplateSuffix());
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
