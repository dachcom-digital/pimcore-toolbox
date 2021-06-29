<?php

namespace ToolboxBundle\Document\Areabrick\Content;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Content extends AbstractAreabrick
{
    public function action(Info $info)
    {
        return parent::action($info);
    }

    public function getName(): string
    {
        return 'WYSIWYG Editor';
    }

    public function getDescription(): string
    {
        return 'Toolbox wysiwyg';
    }
}
