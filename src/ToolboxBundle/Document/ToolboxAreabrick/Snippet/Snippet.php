<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Snippet;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Snippet extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function action(Info $info): ?Response
    {
        if ($this->isHeadlessLayoutAware()) {
            $info->setParam('isHeadless', true);
        }

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
