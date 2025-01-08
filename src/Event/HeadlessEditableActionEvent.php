<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
