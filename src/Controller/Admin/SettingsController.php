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

namespace ToolboxBundle\Controller\Admin;

use Exception;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;

class SettingsController extends AdminAbstractController
{
    public function __construct(protected ConfigManagerInterface $configManager)
    {
    }

    /**
     * @throws Exception
     */
    public function wysiwygAreaStyleAction(): Response
    {
        [$wysiwygObjectConfig, $wysiwygAreaConfig] = $this->parseData();

        $response = $this->render('@Toolbox/admin/settings/wysiwyg-area-style.html.twig', [
            'config' => $wysiwygAreaConfig
        ]);

        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * @throws Exception
     */
    public function wysiwygObjectStyleAction(): Response
    {
        [$wysiwygObjectConfig, $wysiwygAreaConfig] = $this->parseData();

        $response = $this->render('@Toolbox/admin/settings/wysiwyg-object-style.html.twig', [
            'config' => $wysiwygObjectConfig
        ]);

        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * @throws Exception
     */
    private function parseData(): array
    {
        $wysiwygSettings = $this->configManager->getConfig('wysiwyg_editor');

        $wysiwygEditorConfig = $wysiwygSettings['config'];

        //object config
        $userWysiwygEditorObjectConfig = [];
        if (isset($wysiwygSettings['object_editor']['config'])) {
            $userWysiwygEditorObjectConfig = $wysiwygSettings['object_editor']['config'];
        }

        //area config
        $userWysiwygEditorAreaConfig = [];
        if (isset($wysiwygSettings['area_editor']['config'])) {
            $userWysiwygEditorAreaConfig = $wysiwygSettings['area_editor']['config'];
        }

        return [
            $this->parseToolbarConfig($wysiwygEditorConfig, $userWysiwygEditorObjectConfig),
            $this->parseToolbarConfig($wysiwygEditorConfig, $userWysiwygEditorAreaConfig)
        ];
    }

    private function parseToolbarConfig(array $defaultConfig, array $userConfig): array
    {
        return array_replace_recursive($defaultConfig, $userConfig);
    }
}
