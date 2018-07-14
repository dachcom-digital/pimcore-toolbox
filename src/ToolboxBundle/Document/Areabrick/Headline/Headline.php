<?php

namespace ToolboxBundle\Document\Areabrick\Headline;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Headline extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        $anchorName = null;
        $anchorNameElement = $this->getDocumentTag($info->getDocument(), 'input', 'anchor_name');

        if (!$anchorNameElement->isEmpty()) {
            $anchorName = \Pimcore\File::getValidFilename($anchorNameElement->getData());
        }

        $view->anchorName = $anchorName;

    }

    public function getName()
    {
        return 'Headline';
    }

    public function getDescription()
    {
        return 'Toolbox Headline';
    }
}