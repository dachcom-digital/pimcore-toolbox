<?php

namespace ToolboxBundle\Document\Areabrick\Gallery;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Gallery extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();
        $view->galleryId = 'gallery-' . uniqid();
        $view->images = $this->getAssetArray(
            $this->getDocumentTag($info->getDocument(),'multihref', 'images')->getElements()
        );
        $view->elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);
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
            } else if ($element instanceof \Pimcore\Model\Asset\Folder) {
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