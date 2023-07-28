<?php

declare(strict_types=1);

namespace ToolboxBundle\Document\Editable;

use Pimcore\Document\Editable\Block\BlockState;
use Pimcore\Document\Editable\Block\BlockStateStack;
use Pimcore\Model\Document\Editable\Area\Info;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class EditableHandler extends \Pimcore\Document\Editable\EditableHandler
{
    public function renderAreaFrontend(Info $info, array $templateParams = []): string
    {
        $editMode = $this->editmodeResolver->isEditmode();

        // in edit mode, render everything like pimcore want us to
        if ($editMode === true) {
            return parent::renderAreaFrontend($info, $templateParams);
        }

        // check, if brick is headless aware in the first place
        $brick = $this->brickManager->getBrick($info->getId());
        if (!$brick instanceof ToolboxHeadlessAwareBrickInterface) {
            return parent::renderAreaFrontend($info, $templateParams);
        }

        // check if theme config is available. if so, we only allow headless layouts
        if ($brick->isHeadlessLayoutAware() === false) {
            return parent::renderAreaFrontend($info, $templateParams);
        }

        $request = $this->requestHelper->getCurrentRequest();
        $brickInfoRestoreValue = $request->attributes->get(self::ATTRIBUTE_AREABRICK_INFO);

        $request->attributes->set(self::ATTRIBUTE_AREABRICK_INFO, $info);

        $info->setRequest($request);

        $headlessResponse = new HeadlessResponse('editable');

        $brick->headlessAction($info, $headlessResponse);

        \Pimcore::getContainer()->get(EditableWorker::class)->dispatch(
            $headlessResponse,
            $brick->getId(),
            $info->getType(),
            $info->getEditable()->getRealName()
        );

        if ($brickInfoRestoreValue === null) {
            $request->attributes->remove(self::ATTRIBUTE_AREABRICK_INFO);
        } else {
            $request->attributes->set(self::ATTRIBUTE_AREABRICK_INFO, $brickInfoRestoreValue);
        }

        // just return nothing
        return '';
    }

    protected function getBlockState(): BlockState
    {
        return $this->getBlockStateStack()->getCurrentState();
    }

    protected function getBlockStateStack(): BlockStateStack
    {
        return \Pimcore::getContainer()->get(BlockStateStack::class);
    }
}
