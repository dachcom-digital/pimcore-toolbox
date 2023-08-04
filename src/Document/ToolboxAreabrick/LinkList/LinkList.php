<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\LinkList;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class LinkList extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getTemplateDirectoryName(): string
    {
        return 'link_list';
    }

    public function getTemplate(): string
    {
        return sprintf('@Toolbox/areas/%s/view.%s', $this->getTemplateDirectoryName(), $this->getTemplateSuffix());
    }

    public function getName(): string
    {
        return 'Link List';
    }

    public function getDescription(): string
    {
        return 'Toolbox Link List';
    }
}
