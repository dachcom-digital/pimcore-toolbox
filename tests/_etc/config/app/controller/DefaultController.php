<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FrontendController
{
    public function defaultAction(Request $request): Response
    {
        return $this->renderTemplate('default/default.html.twig');
    }

    public function snippetAction(Request $request): Response
    {
        return $this->renderTemplate('default/snippet.html.twig');
    }
}
