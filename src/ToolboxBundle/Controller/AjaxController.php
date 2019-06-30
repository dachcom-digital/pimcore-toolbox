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
    /**
     * @param Request                $request
     * @param LayoutManagerInterface $layoutManager
     *
     * @return Response
     */
    public function gmInfoWindowAction(Request $request, LayoutManagerInterface $layoutManager)
    {
        return $this->render(
            $layoutManager->getAreaTemplatePath('googleMap', 'infoWindow'),
            [
                'mapParams'         => $request->get('mapParams'),
                'googleMapsHostUrl' => $this->getParameter('toolbox_google_maps_host_url')
            ]
        );
    }

    /**
     * @param Request                $request
     * @param ConfigManagerInterface $configManager
     *
     * @return JsonResponse
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
