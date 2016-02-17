<?php

namespace Toolbox\Controller\Plugin;

use \Pimcore\Tool;

class HtmlParser extends \Zend_Controller_Plugin_Abstract {

    protected $enabled = true;

    public function dispatchLoopShutdown() {

        if( !Tool::isHtmlResponse($this->getResponse()) )
        {
            return FALSE;
        }

        $body = $this->getResponse()->getBody();
        $htmlData = $this->getEventData();

        if( isset( $htmlData['header'] ) && !empty( $htmlData['header'] ) )
        {
            $headEndPosition = stripos($body, "</head>");
            if ($headEndPosition !== false) {
                $body = substr_replace($body, $htmlData['header']."</head>", $headEndPosition, 7);
            }

        }
        if( isset( $htmlData['footer'] ) && !empty( $htmlData['footer'] ) )
        {
            $bodyEndPosition = stripos($body, "</body>");
            if ($bodyEndPosition !== false) {
                $body = substr_replace($body, $htmlData['footer']."</body>", $bodyEndPosition, 7);
            }
        }

        $this->getResponse()->setBody($body);


    }

    private function getEventData() {

        $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

        $assetHelper = new \Toolbox\Tools\Asset();
        $assetHelper->setIsBackEnd( $viewRenderer->editmode )->setIsFrontEnd( !$viewRenderer->editmode )->setBaseUrl( $viewRenderer->urlHelper()->hostUrl());

        \Pimcore::getEventManager()->trigger('toolbox.addAsset', $assetHelper);

        return $assetHelper->getHtmlData( );

    }

}