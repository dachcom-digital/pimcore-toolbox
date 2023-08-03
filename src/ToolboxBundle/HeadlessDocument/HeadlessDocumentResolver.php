<?php

namespace ToolboxBundle\HeadlessDocument;

use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Snippet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Editable\EditableJsonFetcher;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Environment;

class HeadlessDocumentResolver
{
    public function __construct(
        protected Environment $environment,
        protected ConfigManagerInterface $configManager,
        protected EditmodeResolver $editmodeResolver,
        protected EditableJsonFetcher $editableJsonFetcher,
        protected AreaManagerInterface $areaManager
    ) {
    }

    public function resolveDocument(Request $request, Document $document, string $headlessDocumentName): Response
    {
        $editMode = $this->editmodeResolver->isEditmode($request);
        $headlessDocumentConfig = $this->configManager->getHeadlessDocumentConfig($headlessDocumentName);

        if (empty($headlessDocumentConfig)) {

            $message = sprintf('Headless document definition "%s" not found', $headlessDocumentName);

            return $editMode
                ? new Response($message, 500)
                : new JsonResponse(['message' => $message], 500);
        }

        if ($editMode === true) {
            return $this->buildEditModeOutput($document, $headlessDocumentName, $headlessDocumentConfig['areas']);
        }

        return $this->buildJsonOutput($document, $headlessDocumentName, $headlessDocumentConfig['areas']);
    }

    private function buildEditModeOutput(Document $document, string $headlessDocumentName, array $areas): Response
    {
        $editModeEditables = [];

        foreach ($areas as $areaName => $areaConfig) {
            if ($areaConfig['type'] === 'areablock') {
                $areaBlockConfig = $this->areaManager->getAreaBlockConfiguration($areaName, $document instanceof Snippet, true);
                $editModeEditables[$areaName] = [$areaConfig['type'], $areaBlockConfig];
            } else {
                $editModeEditables[$areaName] = [
                    $areaConfig['type'],
                    [
                        'type' => $areaConfig['areaType'],
                    ]
                ];
            }
        }

        $response = new Response();
        $editModeView = sprintf('@Toolbox/headless_document/%s.html.twig', $headlessDocumentName);
        $response->setContent($this->environment->render($editModeView, [
            'headless_document_name' => $headlessDocumentName,
            'editables'              => $editModeEditables,
        ]));

        return $response;
    }

    private function buildJsonOutput(Document $document, string $headlessDocumentName, array $areas): JsonResponse
    {
        $editables = [];
        foreach ($areas as $areaName => $areaConfig) {

            $editableConfig = [
                'name' => $areaName,
                'type' => $areaConfig['type'],
            ];

            if ($areaConfig['type'] === 'areablock') {
                // override config with area block config
                $areaBlockConfig = $this->areaManager->getAreaBlockConfiguration($areaName, $document instanceof Snippet, true);
                $editableConfig['config'] = $areaBlockConfig;
            } elseif ($areaConfig['type'] === 'area') {
                $editableConfig['config'] = [
                    'type' => $areaConfig['areaType']
                ];
            } else {
                throw new \Exception(sprintf('Invalid type "%s" in headless document', $areaConfig['type']));
            }

            $editables[] = $editableConfig;
        }

        $editableResponse = $this->editableJsonFetcher->fetchEditablesAsArray($document, $editables, false);

        return new JsonResponse($editableResponse);
    }
}
