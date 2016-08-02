<?php

namespace Toolbox\Controller\Plugin;

class Frontend extends \Zend_Controller_Plugin_Abstract {

    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        parent::preDispatch($request);

        /** @var \Pimcore\Controller\Action\Helper\ViewRenderer $renderer */
        $renderer = \Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
        $renderer->initView();

        /** @var \Pimcore\View $view */
        $view = $renderer->view;
        $view->addScriptPath(PIMCORE_PLUGINS_PATH . '/Toolbox/views/scripts');
        $view->addHelperPath(PIMCORE_PLUGINS_PATH . '/Toolbox/lib/Toolbox/View/Helper', 'Toolbox\View\Helper');
    }

    public function postDispatch(\Zend_Controller_Request_Abstract $request) {

        parent::postDispatch($request);

        $layout = \Zend_Layout::getMvcInstance();

        if( $layout && $layout->isEnabled() !== FALSE)
        {
            \Pimcore::getEventManager()->attach('toolbox.addAsset', function (\Zend_EventManager_Event $e)
            {
                $assetHandler = $e->getTarget();
                $assetHandler->appendScript('toolbox-wysiwyg', '/plugins/Toolbox/static/js/wysiwyg.js', array(), array('showInFrontEnd' => false ));
                $assetHandler->appendScript('toolbox-parallax', '/plugins/Toolbox/static/js/frontend/jquery.parallax-scroll.min.js', array(), array('showInFrontEnd' => true ));
                $assetHandler->appendScript('vimeo-api', '/plugins/Toolbox/static/js/frontend/vimeo-api.min.js', array(), array('showInFrontEnd' => true ));
                $assetHandler->appendScript('toolbox-frontend', '/plugins/Toolbox/static/js/frontend/toolbox.js', array(), array('showInFrontEnd' => true ));

            });

        }

    }

}

