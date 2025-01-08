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

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model\Document;
use Pimcore\Tool\Serialize;

class ColumnAdjuster extends Document\Editable
{
    protected array $data = [];

    public function getType(): string
    {
        return 'columnadjuster';
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function frontend(): void
    {
        // nothing to do.
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function setDataFromResource(mixed $data): static
    {
        $data = Serialize::unserialize($data);

        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;

        return $this;
    }

    public function setDataFromEditmode(mixed $data): static
    {
        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;

        return $this;
    }
}
