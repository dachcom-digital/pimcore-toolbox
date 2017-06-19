<?php

namespace ToolboxBundle\Document\Areabrick\Separator;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Separator extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'Separator';
    }

    public function getDescription()
    {
        return 'Toolbox Separator';
    }
}