<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManager;
use ToolboxBundle\Manager\LayoutManagerInterface;

class AjaxController extends FrontendController
{
    /**
     * @param Request $request
     * @param LayoutManagerInterface $layoutManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gmInfoWindowAction(Request $request, LayoutManagerInterface $layoutManager)
    {
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
     * @param ConfigManagerInterface $configManager
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function videoGetTypesAction(Request $request, ConfigManagerInterface $configManager)
    {
        $videoAreaSettings = $configManager->setAreaNameSpace(ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('video');

        $videoOptions = $videoAreaSettings['video_types'];
        $allowedVideoTypes = [];

        if (!empty($videoOptions)) {
            foreach ($videoOptions as $name => $settings) {
                if ($settings['active'] === true) {
                    $allowedVideoTypes[] = [
                        'name'   => $name,
                        'value'  => $name,
                        'config' => [
                            'allow_lightbox' => $settings['allow_lightbox'],
                            'id_label'       => $settings['id_label']
                        ]
                    ];
                }
            }
        }

        return $this->json($allowedVideoTypes);
    }
}
