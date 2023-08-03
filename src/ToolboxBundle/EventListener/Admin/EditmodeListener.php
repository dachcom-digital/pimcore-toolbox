<?php

namespace ToolboxBundle\EventListener\Admin;

use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Version;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EditmodeListener implements EventSubscriberInterface
{
    public function __construct(
        protected PimcoreContextResolver $contextResolver,
        protected EditmodeResolver $editmodeResolver
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -255],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $scripts = [
            'head'   => sprintf('<script src="%s?_dc=%s"></script>', '/admin/toolbox-wysiwyg-document-style.js', Version::getRevision()),
        ];

        if (!$event->isMainRequest()) {
            return; // only main requests inject edit mode assets
        }

        if (!$this->contextResolver->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        if (!$this->editmodeResolver->isEditmode($request)) {
            return;
        }

        $html = $response->getContent();
        if (!str_contains($html, '<!-- /pimcore editmode -->')) {
            return;
        }

        $html = str_replace(
            '<!-- /pimcore editmode -->',
            sprintf("%s%s<!-- /pimcore editmode -->", $scripts['head'], PHP_EOL),
            $html
        );

        $response->setContent($html);
    }
}
