<?php

class Toolbox_MinifyController extends \Pimcore\Controller\Action\Admin {

    public function renderAction()
    {
        $assetType = $this->getParam('assetType'); //css/js
        $fileExtension = $this->getParam('fileExtension'); //css/js
        $fileName = $this->getParam('fileName'); // xy-z

        $filePath = PIMCORE_TEMPORARY_DIRECTORY . '/' . $fileName . '.' . $fileExtension;

        if ( file_exists($filePath) )
        {
            $response = $this->getResponse()
                ->setHeader('Content-Type', $assetType == 'js' ? 'text/javascript' : 'text/css')
                ->appendBody(file_get_contents($filePath));

            $response->sendResponse();

        }
        else
        {
            echo 'file ' . $filePath . ' does not exists';
        }

        exit;

    }

}
