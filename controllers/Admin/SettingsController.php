<?php

use Pimcore\Controller\Action\Admin;

class Toolbox_Admin_SettingsController extends Admin {

    public function ckeditorStyleAction() {

        $configFile = PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/backend/ckeditor/defaultStyle.json';

        $userConfig = \Toolbox\Config::getConfig()->ckeditor->styles->toArray();
        $defaultConfig = json_decode( file_get_contents($configFile), true );

        $this->view->assign('config', json_encode( array_merge( $defaultConfig, $userConfig) ) );

        $content = $this->view->render('admin/settings/ckeditor-style.php');

        $response = $this->getResponse()
            ->setHeader('Content-Type', 'application/javascript')
            ->appendBody($content);

        $response->sendResponse();

        exit();

    }

}