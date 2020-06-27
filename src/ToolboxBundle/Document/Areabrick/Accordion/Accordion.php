<?php

namespace ToolboxBundle\Document\Areabrick\Accordion;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Accordion extends AbstractAreabrick
{
    /**
     * {@inheritDoc}
     */
    public function action(Info $info)
    {
        parent::action($info);

        $infoParams = $info->getParams();
        if (isset($infoParams['toolboxAccordionId'])) {
            $id = $infoParams['toolboxAccordionId'];
        } else {
            $id = uniqid('accordion-');
        }

        $info->getView()->getParameters()->add(['id' => $id]);

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Accordion';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Accordion / Tabs';
    }
}
