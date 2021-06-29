<?php

namespace ToolboxBundle\Document\Areabrick\Spacer;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Spacer extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName(): string
    {
        return 'Spacer';
    }

    public function getDescription(): string
    {
        return 'Toolbox Spacer';
    }
}
