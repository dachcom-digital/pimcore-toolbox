<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Spacer;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Spacer extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Spacer';
    }

    public function getDescription(): string
    {
        return 'Toolbox Spacer';
    }
}
