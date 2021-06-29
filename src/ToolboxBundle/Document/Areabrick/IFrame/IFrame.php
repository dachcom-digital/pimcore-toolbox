<?php

namespace ToolboxBundle\Document\Areabrick\IFrame;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class IFrame extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $iFrameUrl = $this->getDocumentEditable($info->getDocument(), 'input', 'url')->getData();
        $initialHeight = $this->getDocumentEditable($info->getDocument(), 'numeric', 'iheight')->getData();

        $isValid = true;
        $errorMessage = null;
        if (!empty($iFrameUrl) && $info->getParam('editmode') === true) {
            $response = $this->checkIfUrlIsEmbeddable($iFrameUrl);
            if ($response !== true) {
                $isValid = false;
                $errorMessage = $response;
            }
        }

        $info->setParams([
            'isValid'       => $isValid,
            'errorMessage'  => $errorMessage,
            'initialHeight' => is_numeric($initialHeight) ? (int) $initialHeight : null,
            'iFrameUrl'     => $iFrameUrl
        ]);
    }

    private function checkIfUrlIsEmbeddable(string $iFrameUrl)
    {
        $client = new Client();

        try {
            $response = $client->head($iFrameUrl, ['connect_timeout' => 10]);
            $statusCode = $response->getStatusCode();
        } catch (RequestException $e) {
            return $e->getMessage();
        }

        if ($statusCode !== 200) {
            return false;
        }

        if ($response->hasHeader('X-Frame-Options')) {
            $xFrameOptions = $response->getHeader('X-Frame-Options');
            if (count(array_diff(['SAMEORIGIN', 'DENY'], $xFrameOptions)) !== 0) {
                return false;
            }
        }

        return true;
    }

    public function getName(): string
    {
        return 'iFrame';
    }

    public function getDescription(): string
    {
        return 'Toolbox iFrame';
    }
}
