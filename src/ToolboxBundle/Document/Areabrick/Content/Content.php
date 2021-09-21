<?php

namespace ToolboxBundle\Document\Areabrick\Content;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Content extends AbstractAreabrick
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
        return 'WYSIWYG Editor';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox wysiwyg';
    }
}
