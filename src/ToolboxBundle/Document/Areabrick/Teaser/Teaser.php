<?php

namespace ToolboxBundle\Document\Areabrick\Teaser;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Teaser extends AbstractAreabrick
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

        $flags = $this->configManager->getConfig('flags');
        $useDynamicLinks = $flags['use_dynamic_links'];

        $info->getView()->getParameters()->add(['useDynamicLinks' => $useDynamicLinks]);
    }

    public function getName()
    {
        return 'Teaser';
    }

    public function getDescription()
    {
        return 'Toolbox Teaser';
    }
}
