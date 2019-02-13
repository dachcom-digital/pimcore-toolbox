<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model\Document;

class ColumnAdjuster extends Document\Tag
{
    /**
     * Contains the data.
     *
     * @var bool|array
     */
    public $data = false;

    /**
     * Return the type of the element.
     *
     * @return string
     */
    public function getType()
    {
        return 'columnadjuster';
    }

    /**
     * @see Document\Tag\TagInterface::getData
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * No frontend available.
     */
    public function frontend()
    {
        return null;
    }

    /**
     * @see Document\Tag\TagInterface::admin
     *
     * @return string
     */
    public function admin()
    {
        $html = parent::admin();

        return $html;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function setDataFromResource($data)
    {
        $this->data = \Pimcore\Tool\Serialize::unserialize($data);

        if (!is_array($this->data)) {
            $this->data = false;
        }

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
        if (!is_array($data)) {
            $data = false;
        }

        $this->data = $data;

        return $this;
    }
}
