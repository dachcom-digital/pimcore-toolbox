<?php

namespace ToolboxBundle\Document\Areabrick\Container;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Container extends AbstractAreabrick
{
    public function action(Info $info)
    {
        return parent::action($info);
    }

    public function getName(): string
    {
        return 'Container';
    }

    public function getDescription(): string
    {
        return 'Toolbox Container';
    }
}
