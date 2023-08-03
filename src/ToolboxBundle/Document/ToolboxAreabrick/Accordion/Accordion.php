<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Accordion;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class Accordion extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
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

    private function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        $infoParams = $info->getParams();
        $id = $infoParams['toolboxAccordionId'] ?? str_replace('.', '', uniqid('accordion-', true));

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->addAdditionalConfigData('id', $id);

            return;
        }

        $info->setParam('id', $id);
    }

    public function getName(): string
    {
        return 'Accordion';
    }

    public function getDescription(): string
    {
        return 'Toolbox Accordion / Tabs';
    }
}
