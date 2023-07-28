<?php

namespace ToolboxBundle\Builder;

use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Model\Document\Editable\Area\Info;

interface BrickConfigBuilderInterface
{
    public function buildConfiguration(?Info $info, string $brickId, array $areaConfig = [], array $themeOptions = []): EditableDialogBoxConfiguration;

    public function buildConfigurationData(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = []): array;
}
