<?php

use Pimcore\Controller\Action\Admin;

class Toolbox_Admin_SettingsController extends Admin {

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
        $ckEditorObjectConfig = json_decode( file_get_contents( $ckEditorObjectConfigFile ), TRUE );

        $ckEditorAreaConfigFile = PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/backend/ckeditor/ckEditorAreaConfig.json';
        $ckEditorAreaConfig = json_decode( file_get_contents( $ckEditorAreaConfigFile ), TRUE );

        //object config
        $userCkEditorObjectConfig = [];
        if( isset( $storedConfig->areaEditor )) {
            $userCkEditorObjectConfig = $storedConfig->areaEditor->toArray();
        }

        //area config
        $userCkEditorAreaConfig = [];
        if( isset( $storedConfig->objectEditor )) {
            $userCkEditorAreaConfig = $storedConfig->objectEditor->toArray();
        }

        //global Style Sets config
        if( isset( $storedConfig->globalStyleSets )) {
            $this->globalStyleSets = $storedConfig->globalStyleSets->toArray();
        }

        $this->ckEditorObjectConfig     = array_merge( $ckEditorObjectConfig, $userCkEditorObjectConfig );
        $this->ckEditorAreaConfig       = array_merge( $ckEditorAreaConfig, $userCkEditorAreaConfig );

        parent::init();

    }

    public function ckEditorAreaStyleAction()
    {
        $this->view->assign('config', $this->ckEditorAreaConfig );
        $content = $this->view->render('admin/settings/ckeditor-area-style.php');

        $this->getResponse()
            ->setHeader('Content-Type', 'application/javascript', TRUE)
            ->setBody($content)
            ->sendResponse();

        exit;

    }

    public function ckEditorObjectStyleAction()
    {
        $this->view->assign(
            [
                'globalStyleSets'   => $this->globalStyleSets,
                'config'            =>  $this->ckEditorObjectConfig
            ]
        );
        $content = $this->view->render('admin/settings/ckeditor-object-style.php');

        $this->getResponse()
            ->setHeader('Content-Type', 'application/javascript', TRUE)
            ->setBody($content)
            ->sendResponse();

        exit;

    }

}