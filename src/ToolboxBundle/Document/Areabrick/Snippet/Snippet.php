<?php

namespace ToolboxBundle\Document\Areabrick\Snippet;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Snippet extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'Snippet';
    }

    public function getDescription()
    {
        return 'Toolbox Snippet';
    }
}