<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Model\Document\Editable\Area\Info;
use ToolboxBundle\Document\Response\HeadlessResponse;

interface ToolboxHeadlessAwareBrickInterface
{
    public function isHeadlessLayoutAware(): bool;

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void;
}
