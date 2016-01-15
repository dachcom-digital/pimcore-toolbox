<?php

namespace Toolbox\Controller\Plugin;

use \Pimcore\Tool;

class Assets extends \Zend_Controller_Plugin_Abstract {

    protected $enabled = true;

    public function dispatchLoopShutdown() {

        if( !Tool::isHtmlResponse($this->getResponse()) ) {

            return;

        }

        $body = $this->getResponse()->getBody();
        $string = $this->getEventData();

        $endTag = '</body>' ."\n" . '</html>';

        $replace = $string . $endTag;

        $body = str_replace($endTag, $replace, $body);

        $this->getResponse()->setBody($body);

    }

    private function getEventData() {

        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;

        if( is_null( $view ) )
            return false;

        return $view->footFile()->getHtml( );

    }

}