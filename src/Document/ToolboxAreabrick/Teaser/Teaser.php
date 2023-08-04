<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Teaser;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Teaser extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Teaser';
    }

    public function getDescription(): string
    {
        return 'Toolbox Teaser';
    }
}
