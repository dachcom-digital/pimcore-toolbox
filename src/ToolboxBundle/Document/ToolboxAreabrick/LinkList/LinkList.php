<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\LinkList;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class LinkList extends AbstractAreabrick
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
