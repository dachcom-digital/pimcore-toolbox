<?php

namespace ToolboxBundle\Document\Areabrick\Teaser;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Teaser extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $flags = $this->configManager->getConfig('flags');
        $useDynamicLinks = $flags['use_dynamic_links'];

        $info->setParam('useDynamicLinks', $useDynamicLinks);
    }

    public function getName(): string
    {
        return 'Teaser';
    }

    public function getDescription(): string
    {
        return 'Toolbox Teaser';
    }
}
