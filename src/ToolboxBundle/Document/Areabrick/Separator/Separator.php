<?php

namespace ToolboxBundle\Document\Areabrick\Separator;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Separator extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
    }

    public function getName()
    {
        return 'Separator';
    }

    public function getDescription()
    {
        return 'Toolbox Separator';
    }
}
