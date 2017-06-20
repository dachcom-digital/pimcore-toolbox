<?php

namespace ToolboxBundle\Controller;

use Pimcore\Config;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Controller\FrontendController;
use ToolboxBundle\Service\ConfigManager;

class AjaxController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gmInfoWindowAction(Request $request)
    {
        $mapParams = $request->get('mapParams');
        return $this->render(
            '@Toolbox/Toolbox/GoogleMap/infoWindow.html.twig',
                ['mapParams' => $mapParams]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function videoGetTypesAction(Request $request)
    {
        /** @var ConfigManager $toolboxConfig */
        $toolboxConfig = $this->container->get('toolbox.config_manager');
        $videoAreaSettings = $toolboxConfig->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL)->getAreaParameterConfig('video');

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
