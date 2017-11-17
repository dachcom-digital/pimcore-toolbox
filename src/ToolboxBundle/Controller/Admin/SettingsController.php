<?php

namespace ToolboxBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller;
use ToolboxBundle\Manager\ConfigManager;

class SettingsController extends Controller\AdminController
{
    /**
     * @var array
     */
    public $globalStyleSets = [];

    /**
     * @var array
     */
    public $ckEditorObjectConfig = [];

    /**
     * @var array
     */
    public $ckEditorAreaConfig = [];

    /**
     *
     */
    public function ckEditorAreaStyleAction()
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

    /**
     *
     */
    public function ckEditorObjectStyleAction()
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
     *
     */
    private function setData()
    {
        $toolboxConfig = $this->container->get(ConfigManager::class);
        $ckEditorSettings = $toolboxConfig->getConfig('ckeditor');

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

    /**
     * @param $defaultConfig
     * @param $userConfig
     *
     * @return array
     */
    private function parseToolbarConfig($defaultConfig, $userConfig)
    {
        $config = array_replace_recursive($defaultConfig, $userConfig);
        return $config;
    }
}