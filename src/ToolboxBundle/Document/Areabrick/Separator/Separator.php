<?php

namespace ToolboxBundle\Document\Areabrick\Separator;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Separator extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName(): string
    {
        return 'Separator';
    }

    public function getDescription(): string
    {
        return 'Toolbox Separator';
    }
}
