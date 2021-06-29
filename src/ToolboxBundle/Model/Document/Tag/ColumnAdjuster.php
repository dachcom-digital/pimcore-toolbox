<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model\Document;

class ColumnAdjuster extends Document\Editable
{
    public ?array $data = null;

    public function getType()
    {
        return 'columnadjuster';
    }

    /**
     * @return mixed
     *
     * @see Document\Editable\EditableInterface::getData
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
     * @return string
     *
     * @see Document\Editable\EditableInterface::admin
     */
    public function admin()
    {
        return parent::admin();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->data === false || empty($this->data);
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
     * @param mixed $data
     *
     * @return $this
     *
     * @see Document\Editable\EditableInterface::setDataFromEditmode
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
