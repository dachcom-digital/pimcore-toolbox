<?php

use Toolbox\Controller\Action;

class Toolbox_GoogleMapController extends Action
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        $locale = new \Zend_Locale($this->getParam('language'));
        \Zend_Registry::set('Zend_Locale', $locale);
    }

    /**
     *
     */
    public function infoWindowAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            die('wrong request type');
        }

        $this->disableLayout();
        $this->disableViewAutoRender();

        $mapParams = $this->getParam('mapParams');

        echo $this->view->partial('toolbox/googleMap/infoWindow.php', ['mapParams' => $mapParams]);
    }

}
