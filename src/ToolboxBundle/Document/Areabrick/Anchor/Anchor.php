<?php

namespace ToolboxBundle\Document\Areabrick\Anchor;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

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
