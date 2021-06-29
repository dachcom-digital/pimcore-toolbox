<?php

namespace ToolboxBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;

class SettingsController extends AbstractController
{
    protected ConfigManagerInterface $configManager;
    protected array $globalStyleSets = [];
    protected array $ckEditorObjectConfig = [];
    protected array $ckEditorAreaConfig = [];

    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    public function ckEditorAreaStyleAction(): Response
    {
        $this->setData();

        $response = $this->render('@Toolbox/Admin/Settings/ckeditor-area-style.html.twig', [
            'globalStyleSets' => $this->globalStyleSets,
            'config'          => $this->ckEditorAreaConfig
        ]);

        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function ckEditorObjectStyleAction(): Response
    {
        $this->setData();

        $response = $this->render('@Toolbox/Admin/Settings/ckeditor-object-style.html.twig', [
            'globalStyleSets' => $this->globalStyleSets,
            'config'          => $this->ckEditorObjectConfig
        ]);

        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * @throws \Exception
     */
    private function setData(): void
    {
        $ckEditorSettings = $this->configManager->getConfig('ckeditor');

        $ckEditorGlobalConfig = $ckEditorSettings['config'];

        //object config
        $userCkEditorObjectConfig = [];
        if (isset($ckEditorSettings['object_editor']['config'])) {
            $userCkEditorObjectConfig = $ckEditorSettings['object_editor']['config'];
        }

        //area config
        $userCkEditorAreaConfig = [];
        if (isset($ckEditorSettings['area_editor']['config'])) {
            $userCkEditorAreaConfig = $ckEditorSettings['area_editor']['config'];
        }

        //global Style Sets config
        if (isset($ckEditorSettings['global_style_sets'])) {
            $this->globalStyleSets = $ckEditorSettings['global_style_sets'];
        }

        $this->ckEditorObjectConfig = $this->parseToolbarConfig($ckEditorGlobalConfig, $userCkEditorObjectConfig);
        $this->ckEditorAreaConfig = $this->parseToolbarConfig($ckEditorGlobalConfig, $userCkEditorAreaConfig);
    }

    private function parseToolbarConfig(array $defaultConfig, array $userConfig): array
    {
        return array_replace_recursive($defaultConfig, $userConfig);
    }
}
