<?php

namespace ToolboxBundle\Document\Areabrick\LinkList;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class LinkList extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $flags = $this->configManager->getConfig('flags');
        $useDynamicLinks = $flags['use_dynamic_links'];

        $info->setParam('useDynamicLinks', $useDynamicLinks);
    }

    public function getViewTemplate(): string
    {
        return 'ToolboxBundle:Areas/linkList:view.' . $this->getTemplateSuffix();
    }

    public function getName(): string
    {
        return 'Link List';
    }

    public function getDescription(): string
    {
        return 'Toolbox Link List';
    }
}
