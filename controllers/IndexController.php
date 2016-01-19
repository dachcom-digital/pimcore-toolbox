<?php


class Toolbox_IndexController extends \Pimcore\Controller\Action\Admin {

    public function areaAction() {

        $this->view->extraBricks = $this->_getParam("extraBricks", array());
        $this->view->excludeBricks = $this->_getParam("extraBricks", array());
    }

    public function indexAction () {

        // reachable via http://your.domain/plugin/Toolbox/index/index

    }
}
