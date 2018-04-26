<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model;
use Symfony\Component\EventDispatcher\GenericEvent;

class DynamicLink extends Model\Document\Tag\Link
{
    /**
     * Return the type of the element
     *
     * @return string
     */
    public function getType()
    {
        return 'dynamiclink';
    }

    /**
     * @return string
     */
    public function getHref()
    {
        $url = $this->data['path'];

        if (strpos($url, '::') === false) {
            return parent::getHref();
        }

        $objectInfo = explode('::', $url);
        if (count($objectInfo) !== 2) {
            return parent::getHref();
        }

        if (!\Pimcore\Tool::isFrontend()) {
            return parent::getHref();
        }

        $event = new GenericEvent($this, [
            'className'         => $objectInfo[0],
            'path'              => $objectInfo[1],
            'objectFrontendUrl' => $url
        ]);

        \Pimcore::getEventDispatcher()->dispatch(
            'toolbox.dynamiclink.object.url',
            $event
        );

        return $event->getArgument('objectFrontendUrl');

    }

    /**
     * @return bool
     */
    public function checkValidity()
    {
        if (strpos($this->data['path'], '::') === false) {
            return parent::checkValidity();
        }

        return true;
    }

    /**
     * @param bool $realPath
     * @param bool $editmode
     */
    protected function updatePathFromInternal($realPath = false, $editmode = false)
    {
        if (is_null($this->data['internalId']) && strpos($this->data['path'], '::') !== false) {
            return;
        }

        parent::updatePathFromInternal($realPath, $editmode);
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setDataFromEditmode($data)
    {
        if (strpos($data['path'], '::') === false) {
            parent::setDataFromEditmode($data);
            return $this;
        }

        $data['internal'] = true;
        $data['internalType'] = 'object';

        $this->data = $data;

        return $this;
    }
}