<?php

namespace ToolboxBundle\Document\SimpleAreabrick;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class SimpleAreaBrickConfigurable extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    use SimpleAreaBrickTrait;
}
