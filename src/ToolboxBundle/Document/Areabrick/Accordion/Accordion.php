<?php

namespace ToolboxBundle\Document\Areabrick\Accordion;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Accordion extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $infoParams = $info->getParams();
        if (isset($infoParams['toolboxAccordionId'])) {
            $id = $infoParams['toolboxAccordionId'];
        } else {
            $id = uniqid('accordion-');
        }

        $info->setParam('id', $id);

        return null;
    }

    public function getName(): string
    {
        return 'Accordion';
    }

    public function getDescription(): string
    {
        return 'Toolbox Accordion / Tabs';
    }
}
