<?php

namespace ToolboxBundle\Document\Areabrick\Anchor;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Anchor extends AbstractAreabrick
{
    /**
     * {@inheritdoc}
     */
    public function action(Info $info)
    {
        return parent::action($info);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Anchor';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Anchor';
    }
}
