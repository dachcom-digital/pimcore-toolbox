<?php

namespace ToolboxBundle\Document\Areabrick\Container;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Container extends AbstractAreabrick
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
        return 'Container';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Container';
    }
}
