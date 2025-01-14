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

namespace ToolboxBundle\EventListener\Frontend;

use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Tool;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Templating\EngineInterface;

class FrontendJsListener
{
    use ResponseInjectionTrait;
    use PimcoreContextAwareTrait;

    private EngineInterface $templatingEngine;

    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        if (!Tool::useFrontendOutputFilters()) {
            return;
        }

        $serverVars = $event->getRequest()->server;
        if ($serverVars->get('HTTP_X_PURPOSE') === 'preview') {
            return;
        }

        $response = $event->getResponse();
        if (!$this->isHtmlResponse($response)) {
            return;
        }

        $optOutCookie = $request->cookies->get('tb-google-opt-out-link');

        $codeHead = $this->renderTemplate(
            '@Toolbox/admin/javascript/frontend.html.twig',
            [
                'translations'       => [
                    'toolbox.goptout_already_opt_out',
                    'toolbox.goptout_successfully_opt_out'
                ],
                'trackingIsDisabled' => !empty($optOutCookie),
                'code'               => $optOutCookie
            ]
        );

        $content = $response->getContent();

        if (!empty($codeHead)) {
            $headEndPosition = stripos($content, '</head>');
            if ($headEndPosition !== false) {
                $content = substr_replace($content, $codeHead . '</head>', $headEndPosition, 7);
            }
        }

        $response->setContent($content);
    }

    private function renderTemplate(string $template, array $data): string
    {
        $code = $this->templatingEngine->render(
            $template,
            $data
        );

        $code = trim($code);
        if (!empty($code)) {
            $code = "\n" . $code . "\n";
        }

        return $code;
    }
}
