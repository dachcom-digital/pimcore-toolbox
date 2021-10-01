<?php

namespace ToolboxBundle\Builder;

use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Model\Document\Editable\Area\Info;

interface BrickConfigBuilderInterface
{
    public function buildDialogBoxConfiguration(?Info $info, string $brickId, array $configNode = [], array $themeOptions = []): EditableDialogBoxConfiguration;
}
