<?php

namespace Toolbox;

class HelperController extends \Pimcore\Controller\Action\Admin
{
    public function areaAction()
    {
        $this->view->name = $this->_getParam("name", "image");
    }
}
