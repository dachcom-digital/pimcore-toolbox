<?php

namespace Toolbox\Controller\Plugin;

class Frontend extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @param \Zend_Controller_Request_Abstract $request
     */
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

    /**
     * @param \Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        parent::postDispatch($request);

        $layout = \Zend_Layout::getMvcInstance();

        if ($layout && $layout->isEnabled() !== FALSE) {
            \Pimcore::getEventManager()->attach('toolbox.addAsset', function (\Zend_EventManager_Event $e) {
                $assetHandler = $e->getTarget();

                $assetHandler->appendScript('toolbox-vendor-vimeo-api', '/plugins/Toolbox/static/js/frontend/vendor/vimeo-api.min.js', [], ['showInFrontEnd' => TRUE]);

                $assetHandler->appendScript('toolbox-frontend-main', '/plugins/Toolbox/static/js/frontend/toolbox-main.js', [], ['showInFrontEnd' => TRUE]);
                $assetHandler->appendScript('toolbox-frontend-video', '/plugins/Toolbox/static/js/frontend/toolbox-video.js', [], ['showInFrontEnd' => TRUE]);
                $assetHandler->appendScript('toolbox-frontend-google-maps', '/plugins/Toolbox/static/js/frontend/toolbox-googleMaps.js', [], ['showInFrontEnd' => TRUE]);
            });
        }
    }

}

