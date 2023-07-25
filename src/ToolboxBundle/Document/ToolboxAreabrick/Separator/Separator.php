<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Separator;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class Separator extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
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
