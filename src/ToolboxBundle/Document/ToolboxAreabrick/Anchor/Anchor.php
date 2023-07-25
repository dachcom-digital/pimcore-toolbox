<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Anchor;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class Anchor extends AbstractAreabrick
{
    /**
     * {@inheritdoc}
     */
    public function action(Info $info): ?Response
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
