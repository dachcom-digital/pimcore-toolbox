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

use Twig\TwigFunction;

class DocumentEditableExtension extends \Pimcore\Twig\Extension\DocumentEditableExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_document_tag', [$this, 'renderEditable'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new TwigFunction('pimcore_iterate_block', [$this, 'getBlockIterator'])
        ];
    }
}
