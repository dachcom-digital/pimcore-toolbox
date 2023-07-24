<?php

namespace ToolboxBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManagerInterface;

class AjaxController extends FrontendController
{
    public function gmInfoWindowAction(Request $request, LayoutManagerInterface $layoutManager): Response
    {
        return $this->render(
            $layoutManager->getAreaTemplatePath('googleMap', 'google-map', 'info-window'),
            [
                'mapParams'         => $request->get('mapParams'),
                'googleMapsHostUrl' => $this->getParameter('toolbox_google_maps_host_url')
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function videoGetTypesAction(Request $request, ConfigManagerInterface $configManager): JsonResponse
    {
        $videoAreaSettings = $configManager->getAreaParameterConfig('video');

        $videoOptions = $videoAreaSettings['video_types'];
        $allowedVideoTypes = [];

        if (!empty($videoOptions)) {
            foreach ($videoOptions as $name => $settings) {
                if ($settings['active'] === true) {
                    $allowedVideoTypes[] = [
                        'name'   => $name,
                        'value'  => $name,
                        'config' => [
                            'allow_lightbox' => $settings['allow_lightbox'] ?? false,
                            'id_label'       => $settings['id_label'] ?? null
                        ]
                    ];
                }
            }
        }

        return $this->json($allowedVideoTypes);
    }
}
