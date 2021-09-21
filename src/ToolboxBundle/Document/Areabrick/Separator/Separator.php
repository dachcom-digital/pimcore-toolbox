<?php

namespace ToolboxBundle\Document\Areabrick\Separator;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Separator extends AbstractAreabrick
{
    /**
     * @param Info $info
     *
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     *
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);
    }

    public function getName()
    {
        return 'Separator';
    }

    public function getDescription()
    {
        return 'Toolbox Separator';
    }
}
