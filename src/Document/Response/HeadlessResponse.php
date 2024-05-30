<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Response;

class HeadlessResponse
{
    public const TYPE_BRICK = 'brick';
    public const TYPE_EDITABLE = 'editable';

    protected array $configElementData = [];
    protected array $inlineConfigElementData = [];
    protected array $additionalConfigData = [];
    protected array $brickConfiguration = [];

    public function __construct(
        protected string $type,
        protected ?string $editableType = null,
        protected ?string $brickParent = null,
        protected ?array $editableConfiguration = null,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEditableType(): ?string
    {
        return $this->editableType;
    }

    public function hasBrickParent(): bool
    {
        return $this->brickParent !== null;
    }


    public function getBrickParent(): ?string
    {
        return $this->brickParent;
    }

    public function hasEditableConfiguration(): bool
    {
        return $this->editableConfiguration !== null;
    }

    public function getEditableConfiguration(): ?array
    {
        return $this->editableConfiguration;
    }

    public function getConfigElementData(): array
    {
        return $this->configElementData;
    }

    public function getConfigElementDataItem(string $key): mixed
    {
        if (!array_key_exists($key, $this->configElementData)) {
            return null;
        }

        return $this->configElementData[$key];
    }

    public function setConfigElementData(array $configElementData): void
    {
        if ($this->getType() === self::TYPE_EDITABLE) {
            throw new \Exception('Editables cannot contain config data');
        }

        $this->configElementData = $configElementData;
    }

    public function getInlineConfigElementData(): array
    {
        return $this->inlineConfigElementData;
    }

    public function getInlineConfigElementDataItem(string $key): mixed
    {
        if (!array_key_exists($key, $this->inlineConfigElementData)) {
            return null;
        }

        return $this->inlineConfigElementData[$key];
    }

    public function setInlineConfigElementData(array $inlineConfigElementData): void
    {
        $this->inlineConfigElementData = $inlineConfigElementData;
    }

    public function getAdditionalConfigData(): array
    {
        return $this->additionalConfigData;
    }

    public function setAdditionalConfigData(array $additionalConfigData): void
    {
        if ($this->getType() === self::TYPE_EDITABLE) {
            throw new \Exception('Editables cannot contain additional config data');
        }

        $this->additionalConfigData = $additionalConfigData;
    }

    public function addAdditionalConfigData(string $key, mixed $data): void
    {
        $this->additionalConfigData[$key] = $data;
    }

    public function getBrickConfiguration(): array
    {
        return $this->brickConfiguration;
    }

    public function setBrickConfiguration(array $brickConfiguration): void
    {
        $this->brickConfiguration = $brickConfiguration;
    }
}
