<?php

namespace ToolboxBundle\Document\Areabrick\Accordion;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Accordion extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();
        $view->id = uniqid('accordion-');
        $view->elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);
    }

    public function getName()
    {
        return 'Accordion';
    }

    public function getDescription()
    {
        return 'Toolbox Accordion / Tabs';
    }
}