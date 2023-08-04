<?php

namespace ToolboxBundle\Event;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Contracts\EventDispatcher\Event;
use ToolboxBundle\Document\Response\HeadlessResponse;

class HeadlessEditableActionEvent extends Event
{
    protected Info $info;
    protected HeadlessResponse $headlessResponse;
    protected $editableFinder;

    public function __construct(
        Info $info,
        HeadlessResponse $headlessResponse,
        callable $editableFinder,
    ) {
        $this->info = $info;
        $this->headlessResponse = $headlessResponse;
        $this->editableFinder = $editableFinder;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }

    public function getHeadlessResponse(): HeadlessResponse
    {
        return $this->headlessResponse;
    }

    public function findDocumentEditable(string $type, string $inputName, array $options = [])
    {
        return call_user_func($this->editableFinder, $this->info->getDocument(), $type, $inputName, $options);
    }
}
