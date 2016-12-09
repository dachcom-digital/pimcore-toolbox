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

    /**
     * @return string
     */
    public function getHref()
    {
        $this->updatePathFromInternal();

        $url = $this->data['path'];

        if ($this->data['internalType'] == 'object')
        {
            $objectInfo = explode('::', $url);
            if( count( $objectInfo ) == 2)
            {
                $path = FALSE;
                $cmdEv = \Pimcore::getEventManager()->trigger('toolbox.url.objectFrontendUrl', null, array('className' => $objectInfo[0], 'path' => $objectInfo[1]));

                if ($cmdEv->stopped())
                {
                    $path = $cmdEv->last();
                    if( !empty( $path ) )
                    {
                        $path = \Toolbox\Tool\GlobalLink::parse($path);
                    }
                }

                return $path;
            }
            else
            {
                return FALSE;
            }
        }

        if (strlen($this->data['parameters']) > 0)
        {
            $url .= "?" . str_replace("?", "", $this->getParameters());
        }

        if (strlen($this->data['anchor']) > 0)
        {
            $url .= "#" . str_replace("#", "", $this->getAnchor());
        }

        return $url;
    }

    /**
     * @param bool $realPath
     */
    protected function updatePathFromInternal($realPath = FALSE)
    {
        $method = 'getFullPath';

        if ($realPath)
        {
            $method = 'getRealFullPath';
        }

        if (isset( $this->data['internal'] ) && $this->data['internal'])
        {
            if ($this->data['internalType'] == 'document')
            {
                if ($doc = Document::getById($this->data['internalId']))
                {
                    if (!Document::doHideUnpublished() || $doc->isPublished())
                    {
                        $path = $doc->$method();
                        $this->data['path'] = \Toolbox\Tool\GlobalLink::parse($path);
                    }
                }
            }

            else if ($this->data['internalType'] == 'asset')
            {
                if ($asset = Asset::getById($this->data['internalId']))
                {
                    $this->data['path'] = $asset->$method();
                }

            }

        }

    }

    /**
     * @see Document\Tag\TagInterface::setDataFromEditmode
     * @param mixed $data
     * @return void
     */
    public function setDataFromEditmode($data)
    {
        if (!is_array($data))
        {
            $data = array();
        }

        if ($doc = Document::getByPath($data['path']))
        {
            if ($doc instanceof Document)
            {
                $data['internal'] = TRUE;
                $data['internalId'] = $doc->getId();
                $data['internalType'] = 'document';
            }

            //its an object?
        } else if( strpos($data['path'],'::') !== FALSE)
        {
            $data['internal'] = TRUE;
            $data['internalType'] = 'object';
        }

        if (!$data['internal'])
        {
            if ($asset = Asset::getByPath($data['path']))
            {
                if ($asset instanceof Asset)
                {
                    $data['internal'] = TRUE;
                    $data['internalId'] = $asset->getId();
                    $data['internalType'] = 'asset';
                }
            }
        }

        $this->data = $data;

        return $this;
    }
}