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

namespace ToolboxBundle\Document\SimpleAreabrick;

trait SimpleAreaBrickTrait
{
    protected string $name = '';
    protected string $description = '';
    protected ?string $templatePath = null;
    protected ?string $icon = null;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setTemplate(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    public function getTemplate(): ?string
    {
        return $this->templatePath;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }
}
