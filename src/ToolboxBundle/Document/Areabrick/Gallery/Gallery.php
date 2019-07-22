<?php

namespace ToolboxBundle\Document\Areabrick\Gallery;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Gallery extends AbstractAreabrick
{
    /**
     * @param Info $info
     *
     * @return Response|void|null
     *
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $infoParams = $info->getParams();
        if (isset($infoParams['toolboxGalleryId'])) {
            $id = $infoParams['toolboxGalleryId'];
        } else {
            $id = uniqid('gallery-');
        }

        /** @var \Pimcore\Model\Document\Tag\Relations $imagesField */
        $imagesField = $this->getDocumentTag($info->getDocument(), 'relations', 'images');

        $info->getView()->getParameters()->add([
            'galleryId' => $id,
            'images'    => $this->getAssetArray($imagesField->getElements())
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Gallery';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Gallery';
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getAssetArray($data)
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
