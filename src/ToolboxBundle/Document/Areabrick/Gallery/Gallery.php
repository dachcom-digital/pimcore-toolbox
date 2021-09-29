<?php

namespace ToolboxBundle\Document\Areabrick\Gallery;

use Pimcore\Model\Document\Editable\Relations;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Asset;

class Gallery extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        parent::action($info);

        $infoParams = $info->getParams();
        $id = $infoParams['toolboxGalleryId'] ?? uniqid('gallery-', true);

        /** @var Relations $imagesField */
        $imagesField = $this->getDocumentEditable($info->getDocument(), 'relations', 'images');

        $info->setParams(array_merge($info->getParams(), [
            'galleryId' => $id,
            'images'    => $this->getAssetArray($imagesField->getElements())
        ]));

        return null;
    }

    public function getName(): string
    {
        return 'Gallery';
    }

    public function getDescription(): string
    {
        return 'Toolbox Gallery';
    }

    public function getAssetArray(array $data): array
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
