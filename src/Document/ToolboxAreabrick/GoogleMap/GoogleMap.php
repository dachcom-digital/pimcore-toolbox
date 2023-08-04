<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\GoogleMap;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class GoogleMap extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function __construct(protected string $googleMapsHostUrl = '')
    {
    }

    public function action(Info $info): ?Response
    {
        $this->buildInfoParameters($info);

        return parent::action($info);
    }

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->buildInfoParameters($info, $headlessResponse);

        parent::headlessAction($info, $headlessResponse);
    }

    protected function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->addAdditionalConfigData('googleMapsHostUrl', $this->googleMapsHostUrl);

            return;
        }

        $info->setParam('googleMapsHostUrl', $this->googleMapsHostUrl);
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
