<?php

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

    public function setDataFromResource($data): self
    {
        $data = Serialize::unserialize($data);

        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;

        return $this;
    }

    public function setDataFromEditmode(mixed $data): self
    {
        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;

        return $this;
    }
}
