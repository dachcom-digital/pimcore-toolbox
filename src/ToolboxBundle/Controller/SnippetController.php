<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;

class SnippetController extends FrontendController
{
    /**
     * @param Request                $request
     * @param ConfigManagerInterface $configManager
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function teaserAction(Request $request, ConfigManagerInterface $configManager)
    {
        $layoutStore = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaElementConfig('teaser', 'layout');
        $addClStore = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaElementConfig('teaser', 'additional_classes');
        $flags = $configManager->getConfig('flags');

        $store = $layoutStore['config']['store'];
        $layoutExtJsStore = [];
        if (is_array($store)) {
            foreach ($store as $key => $item) {
                $layoutExtJsStore[] = [$key, $item];
            }
        }

        $store = $addClStore['config']['store'];
        $addClExtJsStore = [];
        if (is_array($store)) {
            foreach ($store as $key => $item) {
                $addClExtJsStore[] = [$key, $item];
            }
        }

        return $this->renderTemplate(
            '@Toolbox/Snippet/Layout/teaser-layout.html.twig',
            [
                'useDynamicLinks'        => $flags['use_dynamic_links'],
                'mapParams'              => $request->get('mapParams'),
                'layoutStore'            => $layoutExtJsStore,
                'additionalClassesStore' => $addClExtJsStore
            ]
        );
    }
}
