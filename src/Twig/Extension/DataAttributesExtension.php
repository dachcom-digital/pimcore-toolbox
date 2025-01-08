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

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Service\DataAttributeService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataAttributesExtension extends AbstractExtension
{
    public function __construct(protected DataAttributeService $dataAttributeService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_data_attributes_generator', [$this->dataAttributeService, 'generateDataAttributes'], [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
