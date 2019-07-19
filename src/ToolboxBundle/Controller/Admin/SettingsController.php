<?php

namespace ToolboxBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;

class SettingsController extends Controller\AdminController
{
    /**
     * @var array
     */
    protected $globalStyleSets = [];

    /**
     * @var array
     */
    protected $ckEditorObjectConfig = [];

    /**
     * @var array
     */
    protected $ckEditorAreaConfig = [];

    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @param ConfigManagerInterface $configManager
     */
    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return Response
     *
     * @throws \Exception
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
     * @return Response
     *
     * @throws \Exception
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
     * @throws \Exception
     */
    private function setData()
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

    /**
     * @param array $defaultConfig
     * @param array $userConfig
     *
     * @return array
     */
    private function parseToolbarConfig($defaultConfig, $userConfig)
    {
        $config = array_replace_recursive($defaultConfig, $userConfig);

        return $config;
    }
}
