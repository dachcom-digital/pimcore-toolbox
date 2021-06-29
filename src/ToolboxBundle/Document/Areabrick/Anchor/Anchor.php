<?php

namespace ToolboxBundle\Document\Areabrick\Anchor;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Anchor extends AbstractAreabrick
{
    public function action(Info $info)
    {
        return parent::action($info);
    }

    public function getName(): string
    {
        return 'Anchor';
    }

    public function getDescription(): string
    {
        return 'Toolbox Anchor';
    }
}
