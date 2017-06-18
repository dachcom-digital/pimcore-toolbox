<?php

namespace ToolboxBundle\Document\Areabrick\GoogleMap;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class GoogleMap extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();
        $view->elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);
    }

    public function getViewTemplate()
    {
        return 'ToolboxBundle:Areas/GoogleMap:view.' . $this->getTemplateSuffix();
    }

    public function getName()
    {
        return 'Google Map';
    }

    public function getDescription()
    {
        return 'Toolbox Google Map';
    }
}