<?php

namespace ToolboxBundle\Document\Areabrick\Container;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Container extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'Container';
    }

    public function getDescription()
    {
        return 'Toolbox Container';
    }
}