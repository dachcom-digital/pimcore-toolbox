<?php

namespace ToolboxBundle\HeadlessDocument;

use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Model\Document;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Editable\ConfigParser;
use ToolboxBundle\Document\Editable\EditableJsonSubscriber;
use ToolboxBundle\Document\Editable\HeadlessEditableRenderer;
use ToolboxBundle\Factory\HeadlessEditableInfoFactory;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Environment;

class HeadlessDocumentResolver
{
    protected ?EditableJsonSubscriber $subscriber = null;

    public function __construct(
        protected Environment $environment,
        protected ConfigManagerInterface $configManager,
        protected ConfigParser $configParser,
        protected EditmodeResolver $editmodeResolver,
        protected EventDispatcherInterface $eventDispatcher,
        protected HeadlessEditableRenderer $headlessEditableRenderer,
        protected HeadlessEditableInfoFactory $editableInfoFactory
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

        return $this->buildJsonOutput($document, $headlessDocumentConfig['areas']);
    }

    private function buildEditModeOutput(Document $document, string $headlessDocumentName, array $areas): Response
    {
        $editModeEditables = [];

        foreach ($areas as $itemName => $item) {

            $item['name'] = $itemName;

            if (!in_array($item['type'], ['areablock', 'area'])) {
                // configuration of standalone editables in headless documents needs to be transformed here,
                // since we don't have any brick action to handle it!
                $item = $this->parseConfigElement($item, $itemName);
            }

            $headlessInfo = $this->editableInfoFactory->createViaEditable($document, $itemName, true, $item);
            $renderedEditable = $this->headlessEditableRenderer->buildEditable($headlessInfo);
            $editable = $this->headlessEditableRenderer->getEditable($headlessInfo);

            if (in_array($headlessInfo->getType(), ['areablock', 'area'])) {
                // will be rendered within brick process workflow
                $configurationView = $renderedEditable;
            } else {
                $configurationView = $this->headlessEditableRenderer->renderStandaloneEditableWithWrapper(
                    $this->headlessEditableRenderer->renderEditableWithWrapper($item['type'], [
                        'item'     => $item,
                        'editable' => $renderedEditable
                    ]),
                    $editable
                );
            }

            $editModeEditables[$itemName] = $configurationView;
        }

        $response = new Response();

        $resolvedTemplate = $this->environment->resolveTemplate([
            sprintf('@Toolbox/headless_document/%s.html.twig', $headlessDocumentName),
            '@Toolbox/headless_document/default.html.twig'
        ]);

        $response->setContent($this->environment->render($resolvedTemplate, [
            'headless_document_name' => $headlessDocumentName,
            'editables'              => $editModeEditables,
        ]));

        return $response;
    }

    private function buildJsonOutput(Document $document, array $areas): JsonResponse
    {
        $this->registerEventSubscriber();

        foreach ($areas as $itemName => $item) {

            $item['name'] = $itemName;
            $headlessInfo = $this->editableInfoFactory->createViaEditable($document, $itemName, false, $item);
            $this->headlessEditableRenderer->buildEditable($headlessInfo);
        }

        $jsonEditables = $this->subscriber->getJsonEditables();

        $this->unregisterEventSubscriber();

        return new JsonResponse($jsonEditables);
    }

    private function registerEventSubscriber(): void
    {
        if (!$this->subscriber) {
            $this->subscriber = new EditableJsonSubscriber();
            $this->eventDispatcher->addSubscriber($this->subscriber);
        }
    }

    private function unregisterEventSubscriber(): void
    {
        if ($this->subscriber) {
            $this->eventDispatcher->removeSubscriber($this->subscriber);
            $this->subscriber = null;
        }
    }

    protected function parseConfigElement(array $config, string $itemName): array
    {
        $editableConfig = $this->configParser->parseConfigElement(null, $itemName, $config, true);

        if ($editableConfig === null) {
            return [];
        }

        if (array_key_exists('children', $config) && is_array($config['children']) && count($config['children']) > 0) {
            $children = [];

            foreach ($config['children'] as $childName => $childConfig) {
                $children[] = $this->parseConfigElement($childConfig, $childName);
            }

            $editableConfig['children'] = $children;
        }

        return $editableConfig;
    }
}
