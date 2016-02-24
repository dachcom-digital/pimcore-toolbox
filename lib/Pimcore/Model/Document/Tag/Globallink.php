<?php

namespace Pimcore\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Tool;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;

class Globallink extends Model\Document\Tag\Link
{

    /**
     * Return the type of the element
     *
     * @return string
     */
    public function getType()
    {
        return 'globallink';
    }

    protected function updatePathFromInternal()
    {
        if ($this->data['internal'])
        {
            if ($this->data['internalType'] == 'document')
            {
                if ($doc = Document::getById($this->data['internalId']))
                {
                    if (!Document::doHideUnpublished() || $doc->isPublished())
                    {
                        $path = $doc->getFullPath();

                        $this->data['path'] = \Toolbox\Tools\GlobalLink::parse($path);
                    }
                }
            }
            else if ($this->data['internalType'] == 'asset')
            {
                if ($asset = Asset::getById($this->data['internalId']))
                {
                    $this->data['path'] = $asset->getFullPath();
                }

            }

        }

    }

}