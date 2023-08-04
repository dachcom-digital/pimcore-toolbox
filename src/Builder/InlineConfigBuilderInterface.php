<?php

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Editable\Area\Info;

interface InlineConfigBuilderInterface
{
    public function buildInlineConfiguration(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $editMode = false);

    public function buildInlineConfigurationData(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = []): array;
}
