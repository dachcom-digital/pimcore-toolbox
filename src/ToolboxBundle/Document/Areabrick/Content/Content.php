<?php

namespace ToolboxBundle\Document\Areabrick\Content;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Content extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'WYSIWYG Editor';
    }

    public function getDescription()
    {
        return 'Toolbox wysiwyg';
    }
}
