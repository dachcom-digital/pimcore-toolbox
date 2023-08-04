<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\IFrame;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class IFrame extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function action(Info $info): ?Response
    {
        $this->buildInfoParameters($info);

        return parent::action($info);
    }

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->buildInfoParameters($info, $headlessResponse);

        parent::headlessAction($info, $headlessResponse);
    }

    protected function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        $iFrameUrl = $this->getDocumentEditable($info->getDocument(), 'input', 'url')->getData();
        $initialHeight = $this->getDocumentEditable($info->getDocument(), 'numeric', 'iheight')->getData();

        $isValid = true;
        $errorMessage = null;
        if (!empty($iFrameUrl) && $info->getEditable()?->getEditmode() === true) {
            $response = $this->checkIfUrlIsEmbeddable($iFrameUrl);
            if ($response !== true) {
                $isValid = false;
                $errorMessage = $response;
            }
        }

        $brickParams = [
            'isValid'       => $isValid,
            'errorMessage'  => $errorMessage,
            'initialHeight' => is_numeric($initialHeight) ? (int) $initialHeight : null,
            'iFrameUrl'     => $iFrameUrl
        ];

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->setAdditionalConfigData($brickParams);

            return;
        }

        $info->setParams(array_merge($info->getParams(), $brickParams));
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
