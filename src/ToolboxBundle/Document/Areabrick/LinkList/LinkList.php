<?php

namespace ToolboxBundle\Document\Areabrick\LinkList;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class LinkList extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
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
