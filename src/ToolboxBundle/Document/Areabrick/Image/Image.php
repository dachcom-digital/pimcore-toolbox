<?php

namespace ToolboxBundle\Document\Areabrick\Image;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Image extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        return parent::action($info);
    }

    public function getName(): string
    {
        return 'Image';
    }

    public function getDescription(): string
    {
        return 'Toolbox Image';
    }
}
