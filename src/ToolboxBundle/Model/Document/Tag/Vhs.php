<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Model\Document;

class Vhs extends Model\Document\Tag\Video
{
    /**
     * @var bool
     */
    public $showAsLightBox = false;

    /**
     * Enum: [asset, youtube, vimeo, dailymotion].
     *
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $videoParameter;

    /**
     * Return the type of the element.
     *
     * @return string
     */
    public function getType()
    {
        return 'vhs';
    }

    /**
     * @return string
     */
    public function getShowAsLightBox()
    {
        return $this->showAsLightBox;
    }

    /**
     * @param array $videoParameter
     *
     * @return $this
     */
    public function setVideoParameter($videoParameter)
    {
        $this->videoParameter = $videoParameter;

        return $this;
    }

    /**
     * @return array
     */
    public function getVideoParameter()
    {
        if (!is_array($this->videoParameter)) {
            return [];
        }

        $parsedParameter = [];
        foreach ($this->videoParameter as $parameter) {
            $parsedParameter[$parameter['key']] = $parameter['value'];
        }

        return $parsedParameter;
    }

    /**
     * @see Document\Tag\TagInterface::getData
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        $data['showAsLightbox'] = $this->showAsLightBox;
        $data['videoParameter'] = $this->videoParameter;

        return $data;
    }

    /**
     * @return array
     */
    public function getDataForResource()
    {
        $data = parent::getDataForResource();
        $data['showAsLightbox'] = $this->showAsLightBox;
        $data['videoParameter'] = $this->videoParameter;

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

        $this->showAsLightBox = $data['showAsLightbox'];
        $this->videoParameter = $data['videoParameter'];

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
            $this->showAsLightBox = $data['showAsLightbox'];
        }

        if ($data['videoParameter']) {
            $this->videoParameter = $data['videoParameter'];
        }

        return $this;
    }
}
