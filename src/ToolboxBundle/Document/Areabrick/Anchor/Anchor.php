<?php

namespace ToolboxBundle\Document\Areabrick\Anchor;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Anchor extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'Anchor';
    }

    public function getDescription()
    {
        return 'Toolbox Anchor';
    }
}