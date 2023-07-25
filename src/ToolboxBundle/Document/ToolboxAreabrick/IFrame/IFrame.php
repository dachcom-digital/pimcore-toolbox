<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\IFrame;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class IFrame extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        parent::action($info);

        $iFrameUrl = $this->getDocumentEditable($info->getDocument(), 'input', 'url')->getData();
        $initialHeight = $this->getDocumentEditable($info->getDocument(), 'numeric', 'iheight')->getData();

        $isValid = true;
        $errorMessage = null;
        if (!empty($iFrameUrl) && $info->getEditable()->getEditmode() === true) {
            $response = $this->checkIfUrlIsEmbeddable($iFrameUrl);
            if ($response !== true) {
                $isValid = false;
                $errorMessage = $response;
            }
        }

        $info->setParams(array_merge($info->getParams(), [
            'isValid'       => $isValid,
            'errorMessage'  => $errorMessage,
            'initialHeight' => is_numeric($initialHeight) ? (int) $initialHeight : null,
            'iFrameUrl'     => $iFrameUrl
        ]));

        return null;
    }

    private function checkIfUrlIsEmbeddable($iFrameUrl): bool|string
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

    public function getTemplateDirectoryName(): string
    {
        return 'iframe';
    }

    public function getTemplate(): string
    {
        return sprintf('@Toolbox/areas/%s/view.%s', $this->getTemplateDirectoryName(), $this->getTemplateSuffix());
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
