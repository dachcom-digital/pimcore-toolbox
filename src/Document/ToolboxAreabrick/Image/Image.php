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

namespace ToolboxBundle\Document\ToolboxAreabrick\Image;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;

class Image extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function getName(): string
    {
        return 'Image';
    }

    public function getDescription(): string
    {
        return 'Toolbox Image';
    }
}
