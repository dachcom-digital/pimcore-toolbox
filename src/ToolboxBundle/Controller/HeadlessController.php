<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
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
