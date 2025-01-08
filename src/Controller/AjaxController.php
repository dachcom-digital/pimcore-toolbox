<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManagerInterface;

class AjaxController extends FrontendController
{
    public function gmInfoWindowAction(Request $request, LayoutManagerInterface $layoutManager): Response
    {
        return $this->render(
            $layoutManager->getAreaTemplatePath('googleMap', 'google_map', 'info_window'),
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
