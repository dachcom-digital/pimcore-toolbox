<?php

namespace ToolboxBundle\Document\Areabrick\IFrame;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class IFrame extends AbstractAreabrick
{
    /**
     * @param Info $info
     *
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();
        $iFrameUrl = $this->getDocumentTag($info->getDocument(), 'input', 'url')->getData();
        $initialHeight = $this->getDocumentTag($info->getDocument(), 'numeric', 'iheight')->getData();

        $isValid = true;
        $errorMessage = null;
        if (!empty($iFrameUrl) && $view->get('editmode') === true) {
            $response = $this->checkIfUrlIsEmbeddable($iFrameUrl);
            if ($response !== true) {
                $isValid = false;
                $errorMessage = $response;
            }
        }

        $view->getParameters()->add([
            'isValid'       => $isValid,
            'errorMessage'  => $errorMessage,
            'initialHeight' => is_numeric($initialHeight) ? (int)$initialHeight : null,
            'iFrameUrl'     => $iFrameUrl
        ]);
    }

    /**
     * @param string $iFrameUrl
     *
     * @return bool|string
     */
    private function checkIfUrlIsEmbeddable($iFrameUrl)
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

    public function getName()
    {
        return 'iFrame';
    }

    public function getDescription()
    {
        return 'Toolbox iFrame';
    }
}
