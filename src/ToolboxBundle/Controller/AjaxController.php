<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use ToolboxBundle\Service\ConfigManager;
use ToolboxBundle\Service\LayoutManager;

class AjaxController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gmInfoWindowAction(Request $request)
    {
        /** @var LayoutManager $layoutManager */
        $layoutManager = $this->container->get('toolbox.layout_manager');

        return $this->render(
            $layoutManager->getAreaTemplatePath('googleMap', 'infoWindow'),
            [
                'mapParams'         => $request->get('mapParams'),
                'googleMapsHostUrl' => $this->container->getParameter('toolbox_google_maps_host_url')
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function videoGetTypesAction(Request $request)
    {
        /** @var ConfigManager $configManager */
        $configManager = $this->container->get('toolbox.config_manager');
        $videoAreaSettings = $configManager->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('video');

        $videoOptions = $videoAreaSettings['videoTypes'];
        $allowedVideoTypes = [];

        if (!empty($videoOptions)) {
            foreach ($videoOptions as $name => $settings) {
                if ($settings['active'] === TRUE) {
                    $allowedVideoTypes[] = ['name' => $name, 'value' => $name];
                }
            }
        }

        return $this->json($allowedVideoTypes);
    }

}
