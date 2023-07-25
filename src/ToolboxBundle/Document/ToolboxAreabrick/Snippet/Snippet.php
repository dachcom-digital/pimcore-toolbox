<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Snippet;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class Snippet extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
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
