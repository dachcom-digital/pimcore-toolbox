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

namespace ToolboxBundle\Model\Document\Editable;

use Pimcore\Model;
use Pimcore\Tool\Serialize;

class Vhs extends Model\Document\Editable\Video
{
    public bool $showAsLightBox = false;
    public array $videoParameter = [];

    public function getType(): string
    {
        return 'vhs';
    }

    public function getShowAsLightBox(): bool
    {
        return $this->showAsLightBox;
    }

    public function getVideoParameter(): array
    {
        $parsedParameter = [];
        foreach ($this->videoParameter as $parameter) {
            $parsedParameter[$parameter['key']] = $parameter['value'];
        }

        return $parsedParameter;
    }

    public function getData(): array
    {
        $data = parent::getData();

        $data['showAsLightbox'] = $this->showAsLightBox;
        $data['videoParameter'] = $this->videoParameter;

        return $data;
    }

    public function getDataForResource(): array
    {
        $data = parent::getDataForResource();

        $data['showAsLightbox'] = $this->showAsLightBox;
        $data['videoParameter'] = $this->videoParameter;

        return $data;
    }

    public function setDataFromResource(mixed $data): static
    {
        parent::setDataFromResource($data);

        if (!empty($data)) {
            $data = Serialize::unserialize($data);
        }

        $this->showAsLightBox = $data['showAsLightbox'] ?? false;
        $this->videoParameter = $data['videoParameter'] ?? [];

        return $this;
    }

    public function setDataFromEditmode(mixed $data): static
    {
        parent::setDataFromEditmode($data);

        if (isset($data['showAsLightbox'])) {
            $this->showAsLightBox = $data['showAsLightbox'];
        }

        if (isset($data['videoParameter']) && is_array($data['videoParameter'])) {
            $this->videoParameter = $data['videoParameter'];
        }

        return $this;
    }
}
