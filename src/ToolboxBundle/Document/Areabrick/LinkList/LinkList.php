<?php

namespace ToolboxBundle\Document\Areabrick\LinkList;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class LinkList extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getViewTemplate()
    {
        return 'ToolboxBundle:Areas/linkList:view.' . $this->getTemplateSuffix();
    }

    public function getName()
    {
        return 'Link List';
    }

    public function getDescription()
    {
        return 'Toolbox Link List';
    }
}