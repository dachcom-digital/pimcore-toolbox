<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Gallery;

use Pimcore\Model\Asset;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Relations;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class Gallery extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
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
        $infoParams = $info->getParams();
        $id = $infoParams['toolboxGalleryId'] ?? str_replace('.', '', uniqid('gallery-', true));

        /** @var Relations $imagesField */
        $imagesField = $this->getDocumentEditable($info->getDocument(), 'relations', 'images');

        $brickParams = [
            'galleryId' => $id,
            'images'    => $this->getAssetArray($imagesField->getElements())
        ];

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->setAdditionalConfigData($brickParams);

            return;
        }

        $info->setParams(array_merge($info->getParams(), $brickParams));
    }

    public function getName(): string
    {
        return 'Gallery';
    }

    public function getDescription(): string
    {
        return 'Toolbox Gallery';
    }

    protected function getAssetArray(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $assets = [];

        foreach ($data as $element) {
            if ($element instanceof Asset\Image) {
                $assets[] = $element;
            } elseif ($element instanceof Asset\Folder) {
                foreach ($element->getChildren() as $child) {
                    if ($child instanceof Asset\Image) {
                        $assets[] = $child;
                    }
                }
            }
        }

        return $assets;
    }
}
