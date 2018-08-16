<?php

namespace ToolboxBundle\Document\Areabrick\Gallery;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

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

        $info->getView()->getParameters()->add([
            'galleryId' => $id,
            'images'    => $this->getAssetArray(
                $this->getDocumentTag($info->getDocument(), 'multihref', 'images')->getElements()
            )
        ]);
    }

    public function getName()
    {
        return 'Gallery';
    }

    public function getDescription()
    {
        return 'Toolbox Gallery';
    }

    /**
     * @param $data
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
