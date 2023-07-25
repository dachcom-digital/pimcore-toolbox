<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Spacer;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class Spacer extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
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
