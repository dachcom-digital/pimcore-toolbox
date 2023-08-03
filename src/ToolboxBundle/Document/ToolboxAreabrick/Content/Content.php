<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Content;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Content extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'WYSIWYG Editor';
    }

    public function getDescription(): string
    {
        return 'Toolbox wysiwyg';
    }
}
