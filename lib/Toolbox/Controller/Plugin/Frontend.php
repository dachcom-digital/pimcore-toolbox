<?php

namespace Toolbox\Controller\Plugin;

use \Pimcore\Model\Document;

class Frontend extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @var bool
     */
    private $addAssets = TRUE;

    /**
     * @var array
     */
    private $printDocTypes = ['printcontainer', 'printpage'];

    /**
     * Frontend constructor.
     *
     * @param bool $addAssets
     */
    public function __construct($addAssets = TRUE) {
        $this->addAssets = $addAssets;
    }

    /**
     * @param \Zend_Controller_Request_Abstract $request
     *
     * @return bool
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

        return TRUE;
    }

    /**
     * @param \Zend_Controller_Request_Abstract $request
     *
     * @return bool
     */
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        parent::postDispatch($request);

        //do nothing if AssetHandler is disabled.
        if($this->addAssets === FALSE) {
            return FALSE;
        }

        $layout = \Zend_Layout::getMvcInstance();
        $document = $request->getParam('document');

        if (   !$document instanceof Document
            || !$layout instanceof \Zend_Layout
            || !$layout->isEnabled()
            || in_array($document->getType(), $this->printDocTypes)
        ) {
            return FALSE;
        }

        \Pimcore::getEventManager()->attach('toolbox.addAsset', function (\Zend_EventManager_Event $e) {

            $assetHandler = $e->getTarget();
            $assetHandler->appendScript('toolbox-vendor-vimeo-api', '/plugins/Toolbox/static/js/frontend/vendor/vimeo-api.min.js', [], ['showInFrontEnd' => TRUE]);
            $assetHandler->appendScript('toolbox-frontend-main', '/plugins/Toolbox/static/js/frontend/toolbox-main.js', [], ['showInFrontEnd' => TRUE]);
            $assetHandler->appendScript('toolbox-frontend-video', '/plugins/Toolbox/static/js/frontend/toolbox-video.js', [], ['showInFrontEnd' => TRUE]);
            $assetHandler->appendScript('toolbox-frontend-google-maps', '/plugins/Toolbox/static/js/frontend/toolbox-googleMaps.js', [], ['showInFrontEnd' => TRUE]);
        });

        return TRUE;
    }

}