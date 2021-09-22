<?php

namespace ToolboxBundle\Document\Areabrick\Snippet;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Snippet extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
    }

    public function getName()
    {
        return 'Snippet';
    }

    public function getDescription()
    {
        return 'Toolbox Snippet';
    }
}
