<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Accordion;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

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

    public function getName(): string
    {
        return 'Accordion';
    }

    public function getDescription(): string
    {
        return 'Toolbox Accordion / Tabs';
    }
}
