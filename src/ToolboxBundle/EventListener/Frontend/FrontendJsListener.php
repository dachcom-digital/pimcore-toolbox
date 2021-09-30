<?php

namespace ToolboxBundle\EventListener\Frontend;

use Pimcore\Tool;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\EnabledTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Templating\EngineInterface;

class FrontendJsListener
{
    use EnabledTrait;
    use ResponseInjectionTrait;
    use PimcoreContextAwareTrait;

    private EngineInterface $templatingEngine;

    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $request = $event->getRequest();
        if (!$event->isMasterRequest()) {
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
