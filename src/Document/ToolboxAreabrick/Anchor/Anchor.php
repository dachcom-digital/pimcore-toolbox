<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Anchor;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Anchor extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Anchor';
    }

    public function getDescription(): string
    {
        return 'Toolbox Anchor';
    }
}
