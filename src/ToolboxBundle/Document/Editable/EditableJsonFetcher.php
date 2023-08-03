<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable;

use Pimcore\Model\Document;
use Pimcore\Templating\Renderer\EditableRenderer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EditableJsonFetcher
{
    protected ?EditableJsonSubscriber $subscriber = null;

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected EditableRenderer $editableRenderer
    ) {
    }

    public function fetchEditablesAsArray(Document $document, array $editables, bool $editMode): array
    {
        $error = null;
        $this->registerEventSubscriber();

        ob_start();

        foreach ($editables as $editable) {

            try {
                $this->editableRenderer->getEditable($document, $editable['type'], $editable['name'], $editable['config'], $editMode)->render();
            } catch (\Throwable $e) {
                $error = $e;
            }
        }

        ob_end_clean();

        $jsonEditables = $error !== null ? [] : $this->subscriber->getJsonEditables();

        $this->unregisterEventSubscriber();

        if ($error !== null) {
            throw $error;
        }

        return $jsonEditables;
    }

    public function fetchEditableAsArray(Document $document, string $editableType, string $editableName, array $editableConfig, bool $editMode): array
    {
        $error = null;
        $this->registerEventSubscriber();

        ob_start();

        try {
            $this->editableRenderer->getEditable($document, $editableType, $editableName, $editableConfig, $editMode)->render();
        } catch (\Throwable $e) {
            $error = $e;
        }

        ob_end_clean();

        $jsonEditables = $error !== null ? [] : $this->subscriber->getJsonEditables();

        $this->unregisterEventSubscriber();

        if ($error !== null) {
            throw $error;
        }

        return $jsonEditables;
    }

    private function registerEventSubscriber(): void
    {
        if (!$this->subscriber) {
            $this->subscriber = new EditableJsonSubscriber();
            $this->eventDispatcher->addSubscriber($this->subscriber);
        }
    }

    private function unregisterEventSubscriber(): void
    {
        if ($this->subscriber) {
            $this->eventDispatcher->removeSubscriber($this->subscriber);
            $this->subscriber = null;
        }
    }
}
