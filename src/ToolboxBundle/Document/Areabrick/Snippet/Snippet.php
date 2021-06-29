<?php

namespace ToolboxBundle\Document\Areabrick\Snippet;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Snippet extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName(): string
    {
        return 'Snippet';
    }

    public function getDescription(): string
    {
        return 'Toolbox Snippet';
    }
}
