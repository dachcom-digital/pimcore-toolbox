<?php

namespace ToolboxBundle\Document\Areabrick\LinkList;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class LinkList extends AbstractAreabrick
{
    /**
     * @param Info $info
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $flags = $this->configManager->getConfig('flags');
        $useDynamicLinks = $flags['use_dynamic_links'];

        $info->getView()->useDynamicLinks = $useDynamicLinks;
    }

    public function getViewTemplate()
    {
        return 'ToolboxBundle:Areas/linkList:view.' . $this->getTemplateSuffix();
    }

    public function getName()
    {
        return 'Link List';
    }

    public function getDescription()
    {
        return 'Toolbox Link List';
    }
}