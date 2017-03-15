<?php

use Pimcore\Controller\Action\Admin;
use Toolbox\Config;

class Toolbox_Admin_SettingsController extends Admin
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
    public function init()
    {
        $storedConfig = \Toolbox\Config::getConfig()->ckeditor;

        $ckEditorObjectConfigFile = PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/backend/ckeditor/ckEditorObjectConfig.json';
        $ckEditorObjectConfig = json_decode(file_get_contents($ckEditorObjectConfigFile), TRUE);

        $ckEditorAreaConfigFile = PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/backend/ckeditor/ckEditorAreaConfig.json';
        $ckEditorAreaConfig = json_decode(file_get_contents($ckEditorAreaConfigFile), TRUE);

        //object config
        $userCkEditorObjectConfig = [];
        if (isset($storedConfig->objectEditor)) {
            $userCkEditorObjectConfig = $storedConfig->objectEditor->toArray();
        }

        //area config
        $userCkEditorAreaConfig = [];
        if (isset($storedConfig->areaEditor)) {
            $userCkEditorAreaConfig = $storedConfig->areaEditor->toArray();
        }

        //global Style Sets config
        if (isset($storedConfig->globalStyleSets)) {
            $this->globalStyleSets = $storedConfig->globalStyleSets->toArray();
        }

        $this->ckEditorObjectConfig = $this->parseToolbarConfig($ckEditorObjectConfig, $userCkEditorObjectConfig);
        $this->ckEditorAreaConfig = $this->parseToolbarConfig($ckEditorAreaConfig, $userCkEditorAreaConfig);

        parent::init();
    }

    /**
     *
     */
    public function ckEditorAreaStyleAction()
    {
        $this->view->assign(
            [
                'globalStyleSets' => $this->globalStyleSets,
                'config'          => $this->ckEditorAreaConfig
            ]
        );

        $content = $this->view->render('admin/settings/ckeditor-area-style.php');

        $this->getResponse()
            ->setHeader('Content-Type', 'application/javascript', TRUE)
            ->setBody($content)
            ->sendResponse();

        exit;
    }

    /**
     *
     */
    public function ckEditorObjectStyleAction()
    {
        $this->view->assign(
            [
                'globalStyleSets' => $this->globalStyleSets,
                'config'          => $this->ckEditorObjectConfig
            ]
        );

        $content = $this->view->render('admin/settings/ckeditor-object-style.php');

        $this->getResponse()
            ->setHeader('Content-Type', 'application/javascript', TRUE)
            ->setBody($content)
            ->sendResponse();

        exit;
    }

    /**
     * @param $defaultConfig
     * @param $userConfig
     *
     * @return array
     */
    private function parseToolbarConfig($defaultConfig, $userConfig)
    {
        $type = isset($userConfig['toolbarModification']) && !empty($userConfig['toolbarModification']) ? $userConfig['toolbarModification'] : NULL;

        $config = array_merge($defaultConfig, $userConfig);

        unset($config['toolbarModification']);

        if ($type === 'append' && is_array($userConfig['toolbar'])) {
            foreach ($userConfig['toolbar'] as $array) {
                array_push($defaultConfig['toolbar'], $array);
            }

            $config['toolbar'] = $defaultConfig['toolbar'];
        } else if ($type === 'prepend' && is_array($userConfig['toolbar'])) {
            foreach ($userConfig['toolbar'] as $array) {
                array_unshift($defaultConfig['toolbar'], $array);
            }

            $config['toolbar'] = $defaultConfig['toolbar'];
        } else if ($type === 'replace' && is_array($userConfig['toolbar'])) {
            $config['toolbar'] = $userConfig['toolbar'];
        }

        return $config;
    }

    /**
     *
     */
    public function allowedVideoTypesAction()
    {

        $videoOptions = Config::getConfig()->video->videoOptions;
        $allowedVideoTypes = [];

        if (!empty($videoOptions)) {
            foreach ($videoOptions as $name => $settings) {
                if($settings->active === TRUE) {
                    $allowedVideoTypes[] = ['name' => $name, 'value' => $name];
                }
            }
        }
        $this->_helper->json(
            $allowedVideoTypes
        );
    }
}