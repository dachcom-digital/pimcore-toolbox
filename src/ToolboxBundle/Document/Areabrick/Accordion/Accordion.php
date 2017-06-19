<?php

namespace ToolboxBundle\Document\Areabrick\Accordion;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Accordion extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);
        $info->getView()->id = uniqid('accordion-');
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