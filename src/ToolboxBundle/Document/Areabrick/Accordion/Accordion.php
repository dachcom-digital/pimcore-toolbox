<?php

namespace ToolboxBundle\Document\Areabrick\Accordion;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Accordion extends AbstractAreabrick
{
    /**
     * {@inheritdoc}
     */
    public function action(Info $info): ?Response
    {
        parent::action($info);

        $infoParams = $info->getParams();
        if (isset($infoParams['toolboxAccordionId'])) {
            $id = $infoParams['toolboxAccordionId'];
        } else {
            $id = str_replace('.', '', uniqid('accordion-', true));
        }

        $info->setParam('id', $id);

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
