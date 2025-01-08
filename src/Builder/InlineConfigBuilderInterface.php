<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Builder;

use Pimcore\Model\Document\Editable\Area\Info;

interface InlineConfigBuilderInterface
{
    public function buildInlineConfiguration(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = [], bool $editMode = false);

    public function buildInlineConfigurationData(Info $info, string $brickId, array $areaConfig = [], array $themeOptions = []): array;
}
