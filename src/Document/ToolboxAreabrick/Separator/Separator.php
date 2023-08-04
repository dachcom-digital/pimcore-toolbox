<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Separator;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Separator extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Separator';
    }

    public function getDescription(): string
    {
        return 'Toolbox Separator';
    }
}
