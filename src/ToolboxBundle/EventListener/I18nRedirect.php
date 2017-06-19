<?php

namespace ToolboxBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Pimcore\Service\Request\DocumentResolver;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Service\Request\PimcoreContextResolver;
use ToolboxBundle\Service\I18nManager;

class I18nRedirect
{
    use PimcoreContextAwareTrait;

    /**
     * @var DocumentResolver
     */
    protected $documentResolver;

    /**
     * @var I18nManager
     */
    protected $i18nManager;

    /**
     * I18nRedirect constructor.
     *
     * @param DocumentResolver $documentResolver
     * @param I18nManager      $i18nManager
     */
    public function __construct(DocumentResolver $documentResolver, I18nManager $i18nManager)
    {
        $this->i18nManager = $i18nManager;
        $this->documentResolver = $documentResolver;
    }

    /**
     * Redirect Pimcore Link Url to right i18n context.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelRequest(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        $document = $this->documentResolver->getDocument($request);
        if (!$document) {
            return;
        }

        $path = $event->getRequest()->get('path');
        if (empty($path)) {
            return;
        }

        if ($document instanceof \Pimcore\Model\Document\Hardlink\Wrapper\Link) {
            $originDocument = $document->getHardLinkSource();
            $url = $this->i18nManager->checkPath( $event->getRequest()->get('path'));
            if($url !== FALSE) {
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }
}