<?php

namespace ToolboxBundle\Document\Areabrick\Headline;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Headline extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $anchorName = null;
        /** @var \Pimcore\Model\Document\Editable\Input $anchorNameElement */
        $anchorNameElement = $this->getDocumentEditable($info->getDocument(), 'input', 'anchor_name');

        if (!$anchorNameElement->isEmpty()) {
            $anchorName = \Pimcore\File::getValidFilename($anchorNameElement->getData());
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
