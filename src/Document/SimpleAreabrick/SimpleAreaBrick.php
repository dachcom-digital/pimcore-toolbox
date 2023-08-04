<?php

namespace ToolboxBundle\Document\SimpleAreabrick;

use ToolboxBundle\Document\Areabrick\AbstractBaseAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class SimpleAreaBrick extends AbstractBaseAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    use SimpleAreaBrickTrait;
}
