<?php

namespace ToolboxBundle\Document\Areabrick\Spacer;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Spacer extends AbstractAreabrick
{
    /**
     * @param Info $info
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'Spacer';
    }

    public function getDescription()
    {
        return 'Toolbox Spacer';
    }
}