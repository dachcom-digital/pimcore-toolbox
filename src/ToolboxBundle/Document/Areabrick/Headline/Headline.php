<?php

namespace ToolboxBundle\Document\Areabrick\Headline;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Headline extends AbstractAreabrick
{
    /**
     * @param Info $info
     *
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     *
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $anchorName = null;
        /** @var \Pimcore\Model\Document\Tag\Input $anchorNameElement */
        $anchorNameElement = $this->getDocumentTag($info->getDocument(), 'input', 'anchor_name');

        if (!$anchorNameElement->isEmpty()) {
            $anchorName = \Pimcore\File::getValidFilename($anchorNameElement->getData());
        }

        $info->getView()->getParameters()->add(['anchorName' => $anchorName]);
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
