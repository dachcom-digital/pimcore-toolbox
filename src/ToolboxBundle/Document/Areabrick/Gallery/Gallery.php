<?php

namespace ToolboxBundle\Document\Areabrick\Gallery;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Gallery extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $infoParams = $info->getParams();
        if (isset($infoParams['toolboxGalleryId'])) {
            $id = $infoParams['toolboxGalleryId'];
        } else {
            $id = uniqid('gallery-');
        }

        /** @var \Pimcore\Model\Document\Editable\Relations $imagesField */
        $imagesField = $this->getDocumentEditable($info->getDocument(), 'relations', 'images');

        $info->setParams([
            'galleryId' => $id,
            'images'    => $this->getAssetArray($imagesField->getElements())
        ]);
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
            if ($element instanceof \Pimcore\Model\Asset\Image) {
                $assets[] = $element;
            } elseif ($element instanceof \Pimcore\Model\Asset\Folder) {
                foreach ($element->getChildren() as $child) {
                    if ($child instanceof \Pimcore\Model\Asset\Image) {
                        $assets[] = $child;
                    }
                }
            }
        }

        return $assets;
    }
}
