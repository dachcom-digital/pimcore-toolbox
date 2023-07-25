<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Container;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class Container extends AbstractAreabrick
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
        return 'Container';
    }

    public function getDescription(): string
    {
        return 'Toolbox Container';
    }
}
