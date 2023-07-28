<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Image;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Image extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Image';
    }

    public function getDescription(): string
    {
        return 'Toolbox Image';
    }
}
