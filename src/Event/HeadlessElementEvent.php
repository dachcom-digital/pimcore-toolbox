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

namespace ToolboxBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class HeadlessElementEvent extends Event
{
    public function __construct(
        protected array $data,
        protected string $elementType,
        protected string $elementSubType,
        protected ?string $elementHash,
        protected string $elementNamespace
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getElementType(): string
    {
        return $this->elementType;
    }

    public function getElementSubType(): string
    {
        return $this->elementSubType;
    }

    public function getElementHash(): ?string
    {
        return $this->elementHash;
    }

    public function getElementNamespace(): string
    {
        return $this->elementNamespace;
    }
}
