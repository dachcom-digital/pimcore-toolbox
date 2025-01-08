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
use Symfony\Component\HttpFoundation\Request;
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
