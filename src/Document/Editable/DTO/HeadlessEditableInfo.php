<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable\DTO;

use Pimcore\Model\Document;

class HeadlessEditableInfo
{
    public function __construct(
        protected Document $document,
        protected mixed $editableId,
        protected string $name,
        protected string $type,
        protected ?string $label,
        protected ?string $brickParent = null,
        protected ?array $editableConfiguration = null,
        protected array $config = [],
        protected array $params = [],
        protected array $children = [],
        protected bool $editMode = false,
        protected bool $standAloneAware = false
    ) {
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getId(): mixed
    {
        return $this->editableId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getBrickParent(): ?string
    {
        return $this->brickParent;
    }

    public function getEditableConfiguration(): ?array
    {
        return $this->editableConfiguration;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name): mixed
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * @return array<int, HeadlessEditableInfo>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function isEditMode(): bool
    {
        return $this->editMode;
    }

    public function isStandAlone(): bool
    {
        if ($this->isBlockEditable() === true) {
            return false;
        }

        return $this->standAloneAware;
    }

    public function isBlockEditable(): bool
    {
        return in_array($this->getType(), ['area', 'block', 'areablock', 'scheduledblock'], true);
    }
}
