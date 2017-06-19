<?php

namespace ToolboxBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use ToolboxBundle\Service\I18nManager;

class FrontendPathListener
{
    /**
     * @var I18nManager
     */
    protected $i18nManager;

    /**
     * FrontendPathListener constructor.
     *
     * @param I18nManager $i18nManager
     */
    public function __construct(I18nManager $i18nManager)
    {
        $this->i18nManager = $i18nManager;
    }

    /**
     * Valid Paths:
     *
     * /de/test
     * /global-de/test
     * /de-de/test
     *
     * @param GenericEvent $e
     *
     * @return void
     */
    public function checkPath(GenericEvent $e)
    {
        $frontEndPath = $e->getArgument('frontendPath');
        $i18nFrontEndPath = $this->i18nManager->checkPath($frontEndPath);

        if($i18nFrontEndPath !== FALSE) {
            $e->setArgument('frontendPath', $i18nFrontEndPath);
        }
    }
}