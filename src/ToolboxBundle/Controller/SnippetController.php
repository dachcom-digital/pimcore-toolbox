<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use ToolboxBundle\Service\ConfigManager;

class SnippetController extends FrontendController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teaserAction(Request $request)
    {
        /** @var ConfigManager $configManager */
        $configManager = $this->container->get(ConfigManager::class);
        $layoutStore = $configManager->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL)->getAreaElementConfig('teaser', 'layout');
        $addClStore = $configManager->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL)->getAreaElementConfig('teaser', 'additional_classes');

        $store = $layoutStore['config']['store'];
        $layoutExtJsStore = [];
        if(is_array($store)) {
            foreach ($store as $key => $item) {
                $layoutExtJsStore[] = [$key, $item];
            }
        }

        $store = $addClStore['config']['store'];
        $addClExtJsStore = [];
        if(is_array($store)) {
            foreach ($store as $key => $item) {
                $addClExtJsStore[] = [$key, $item];
            }
        }

        return $this->renderTemplate(
            '@Toolbox/Snippet/Layout/teaser-layout.html.twig',
            [
                'mapParams'              => $request->get('mapParams'),
                'layoutStore'            => $layoutExtJsStore,
                'additionalClassesStore' => $addClExtJsStore
            ]
        );
    }
}
