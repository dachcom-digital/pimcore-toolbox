<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Container;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Container extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Container';
    }

    public function getDescription(): string
    {
        return 'Toolbox Container';
    }
}
