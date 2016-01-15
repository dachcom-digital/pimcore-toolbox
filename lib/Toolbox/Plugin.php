<?php

namespace Toolbox;

use Pimcore\API\Plugin as PluginLib;

use Toolbox\Plugin\Install;

class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface {

    public function preDispatch($e) {

        $e->getTarget()->registerPlugin(new Controller\Plugin\Assets());
        $e->getTarget()->registerPlugin(new Controller\Plugin\Frontend());

    }

    public function init() {

        parent::init();


    }

    public function handleDocument ($event) {

        // do something
        //$document = $event->getTarget();

    }

	public static function install () {

        $install = new Install();

        $install->addUserData();

        return 'Toolbox has been successfully installed.';

	}

	public static function uninstall () {

        return true;

	}

	public static function isInstalled () {

        $userM = new \Pimcore\Model\User();
        $user = $userM->getByName('kunde');

        return $user !== FALSE;

	}

}
