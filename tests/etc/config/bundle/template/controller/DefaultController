<?php

namespace AppBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class DefaultController extends FrontendController
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->setViewAutoRender($event->getRequest(), true, 'twig');
    }

    public function defaultAction(Request $request)
    {
    }

    public function snippetAction(Request $request)
    {
    }
}
