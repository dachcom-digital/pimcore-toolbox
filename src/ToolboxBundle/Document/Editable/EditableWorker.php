<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable;

use Pimcore\Document\Editable\Block\BlockState;
use Pimcore\Document\Editable\Block\BlockStateStack;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class EditableWorker
{
    public const EDITABLE_JSON_RESPONSE = 'toolbox.editable.json_response';

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected BlockStateStack $blockStateStack
    ) {
    }

    public function dispatch(HeadlessResponse $data, $id, $type, $name, bool $isSimpleEditable = false): void
    {
        $this->eventDispatcher->dispatch(
            new GenericEvent(
                $data->toArray(),
                [
                    'brickId'          => $id,
                    'indexes'          => $this->getBlockState()->getIndexes(),
                    'blocks'           => $this->getBlockState()->getBlocks(),
                    'name'             => $name,
                    'type'             => $type,
                    'isSimpleEditable' => $isSimpleEditable
                ]
            ),
            self::EDITABLE_JSON_RESPONSE
        );
    }

    public function getCurrentDepth(): int
    {
        return count($this->getBlockState()->getBlocks());
    }

    protected function getBlockState(): BlockState
    {
        return $this->blockStateStack->getCurrentState();
    }
}
