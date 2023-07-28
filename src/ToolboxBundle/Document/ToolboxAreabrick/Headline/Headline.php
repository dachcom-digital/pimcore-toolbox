<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Headline;

use Pimcore\File;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Input;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class Headline extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
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
        $anchorName = null;
        /** @var Input $anchorNameElement */
        $anchorNameElement = $this->getDocumentEditable($info->getDocument(), 'input', 'anchor_name');

        if (!$anchorNameElement->isEmpty()) {
            $anchorName = File::getValidFilename($anchorNameElement->getData());
        }

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->addAdditionalConfigData('anchorName', $anchorName);

            return;
        }

        $info->setParam('anchorName', $anchorName);
    }

    public function getName(): string
    {
        return 'Headline';
    }

    public function getDescription(): string
    {
        return 'Toolbox Headline';
    }
}
