<?php

use Pimcore\Controller\Action\Admin;

class Toolbox_Admin_SettingsController extends Admin {

    public function ckeditorStyleAction() {

        $configFile = PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/backend/ckeditor/defaultStyle.json';

        $config = file_get_contents($configFile);

        $configEvent = \Pimcore::getEventManager()->trigger('toolbox.ckeditorStyle', null, ['config' => json_decode($config, true)]);

        if ($configEvent->stopped()) {
            $config = json_encode($configEvent->last());
        }

        $this->view->assign('config', $config);

        $content = $this->view->render('admin/settings/ckeditor-style.php');

        $response = $this->getResponse()
            ->setHeader('Content-Type', 'application/javascript')
            ->appendBody($content);

        $response->sendResponse();

        exit();

    }

}