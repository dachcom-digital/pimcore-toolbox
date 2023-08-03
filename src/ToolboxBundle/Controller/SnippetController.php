<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;

class SnippetController extends FrontendController
{
    /**
     * @throws \Exception
     */
    public function teaserAction(Request $request, ConfigManagerInterface $configManager): Response
    {
        $layoutStore = $configManager->getAreaElementConfig('teaser', 'layout');
        $addClStore = $configManager->getAreaElementConfig('teaser', 'additional_classes');

        $layoutExtJsStore = [];
        $addClExtJsStore = [];

        $store = $layoutStore['config']['store'];
        if (is_array($store)) {
            foreach ($store as $key => $item) {
                $layoutExtJsStore[] = [$key, $item];
            }
        }

        $store = $addClStore['config']['store'];
        if (is_array($store)) {
            foreach ($store as $key => $item) {
                $addClExtJsStore[] = [$key, $item];
            }
        }

        return $this->renderTemplate(
            '@Toolbox/snippet/layout/teaser_layout.html.twig',
            [
                'mapParams'              => $request->get('mapParams'),
                'layoutStore'            => $layoutExtJsStore,
                'additionalClassesStore' => $addClExtJsStore
            ]
        );
    }
}
