<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Headline;

use Pimcore\File;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Input;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class Headline extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        parent::action($info);

        $anchorName = null;
        /** @var Input $anchorNameElement */
        $anchorNameElement = $this->getDocumentEditable($info->getDocument(), 'input', 'anchor_name');

        if (!$anchorNameElement->isEmpty()) {
            $anchorName = File::getValidFilename($anchorNameElement->getData());
        }

        $info->setParam('anchorName', $anchorName);

        return null;
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
