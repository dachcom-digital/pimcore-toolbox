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
            'head' => [],
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

        $scripts['head'][] = sprintf(
            '<script src="%s?_dc=%s&tb_document_request_id=%d"></script>',
            '/admin/toolbox-wysiwyg-document-style.js',
            Version::getRevision(),
            $request->attributes->get('contentDocument')?->getId()
        );

        $html = str_replace(
            '<!-- /pimcore editmode -->',
            sprintf('%s%s<!-- /pimcore editmode -->', implode(PHP_EOL, $scripts['head']), PHP_EOL),
            $html
        );

        $response->setContent($html);
    }
}
