<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Response;

class HeadlessResponse
{
    protected string $type;

    protected bool $loadConfigElementData = true;
    protected bool $loadInlineConfigElementData = true;

    protected array $configElementData = [];
    protected array $inlineConfigElementData = [];
    protected array $additionalConfigData = [];
    protected array $brickConfiguration = [];

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function loadConfigElementData(): bool
    {
        return $this->loadConfigElementData;
    }

    public function setLoadConfigElementData(bool $loadConfigElementData): void
    {
        $this->loadConfigElementData = $loadConfigElementData;
    }

    public function loadInlineConfigElementData(): bool
    {
        return $this->loadInlineConfigElementData;
    }

    public function setLoadInlineConfigElementData(bool $loadInlineConfigElementData): void
    {
        $this->loadInlineConfigElementData = $loadInlineConfigElementData;
    }

    public function getConfigElementData(): array
    {
        return $this->configElementData;
    }

    public function setConfigElementData(array $configElementData): void
    {
        $this->configElementData = $configElementData;
    }

    public function getInlineConfigElementData(): array
    {
        return $this->inlineConfigElementData;
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

    public function toArray(): array
    {
        return [
            'type'               => $this->type,
            'brickConfiguration' => $this->getBrickConfiguration(),
            'editableData'       => array_merge(
                $this->getConfigElementData(),
                $this->getInlineConfigElementData(),
                $this->getAdditionalConfigData()
            ),
        ];
    }
}
