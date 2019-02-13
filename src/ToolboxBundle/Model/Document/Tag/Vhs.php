<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Model\Document;

class Vhs extends Model\Document\Tag\Video
{
    /**
     * @var bool
     */
    public $showAsLightbox = false;

    /**
     * Enum: [asset, youtube, vimeo, dailymotion]
     *
     * @var string
     */
    public $type = '';

    /**
     * Return the type of the element
     *
     * @return string
     */
    public function getType()
    {
        return 'vhs';
    }

    /**
     * @param bool $showAsLightbox
     *
     * @return $this
     */
    public function setShowAsLightbox($showAsLightbox)
    {
        $this->showAsLightbox = $showAsLightbox;

        return $this;
    }

    /**
     * @return string
     */
    public function getShowAsLightbox()
    {
        return $this->showAsLightbox;
    }

    /**
     * @see Document\Tag\TagInterface::getData
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();
        $data['showAsLightbox'] = $this->showAsLightbox;

        return $data;
    }

    /**
     *
     */
    public function getDataForResource()
    {
        $data = parent::getDataForResource();
        $data['showAsLightbox'] = $this->showAsLightbox;

        return $data;
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromResource
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setDataFromResource($data)
    {
        parent::setDataFromResource($data);

        if (!empty($data)) {
            $data = \Pimcore\Tool\Serialize::unserialize($data);
        }

        $this->showAsLightbox = $data['showAsLightbox'];

        return $this;
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromEditmode
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setDataFromEditmode($data)
    {
        parent::setDataFromEditmode($data);

        if ($data['showAsLightbox']) {
            $this->showAsLightbox = $data['showAsLightbox'];
        }

        return $this;
    }

}