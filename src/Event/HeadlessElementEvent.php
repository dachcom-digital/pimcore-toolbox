<?php

namespace ToolboxBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class HeadlessElementEvent extends Event
{
    public function __construct(
        protected array $data,
        protected string $elementType,
        protected string $elementSubType,
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

    public function getElementNamespace(): string
    {
        return $this->elementNamespace;
    }
}
