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

namespace ToolboxBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\HeadlessDocument\HeadlessDocumentResolver;

class HeadlessController extends FrontendController
{
    public function headlessDocumentAction(Request $request, HeadlessDocumentResolver $headlessDocumentResolver): Response
    {
        $headlessDocumentName = $this->document->getProperty('headless_document');

        if ($headlessDocumentName === null) {
            $headlessDocumentName = 'index';
        }

        return $headlessDocumentResolver->resolveDocument($request, $this->document, $headlessDocumentName);
    }
}
