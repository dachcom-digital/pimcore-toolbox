<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxInterface;
use Pimcore\Model\Document;
use ToolboxBundle\Builder\BrickConfigBuilderInterface;

abstract class AbstractAreabrick extends AbstractBaseAreabrick implements EditableDialogBoxInterface
{
    protected BrickConfigBuilderInterface $brickConfigBuilder;

    public function setBrickConfigBuilder(BrickConfigBuilderInterface $brickConfigBuilder): void
    {
        $this->brickConfigBuilder = $brickConfigBuilder;
    }

    public function getEditableDialogBoxConfiguration(Document\Editable $area, ?Document\Editable\Area\Info $info): EditableDialogBoxConfiguration
    {
        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());
        $themeOptions = $this->getConfigManager()->getConfig('theme');

        return $this->brickConfigBuilder->buildDialogBoxConfiguration($info, $this->getId(), $configNode, $themeOptions);
    }
}
