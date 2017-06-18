<?php

namespace ToolboxBundle\Document\Areabrick\Image;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Image extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();
        $view->elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);
    }

    public function getName()
    {
        return 'Image';
    }

    public function getDescription()
    {
        return 'Toolbox Image';
    }
}