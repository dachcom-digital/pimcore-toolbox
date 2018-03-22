<?php

namespace ToolboxBundle\EventListener\Frontend;

use Pimcore\Tool;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\EnabledTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Templating\EngineInterface;

class FrontendJsTranslationsListener
{
    use EnabledTrait;
    use ResponseInjectionTrait;
    use PimcoreContextAwareTrait;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * FrontendJsTranslationsListener constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param EngineInterface          $templatingEngine
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EngineInterface $templatingEngine
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
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

        $codeHead = $this->renderTemplate(
            '@Toolbox/Admin/Javascript/translations.html.twig',
            [
                'translations' => [
                    'toolbox.goptout_alreay_opt_out',
                    'toolbox.goptout_successfully_opt_out'
                ]
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

    /**
     * @param string $template
     * @param array  $data
     * @return string
     */
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
