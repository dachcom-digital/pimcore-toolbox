<?php

namespace ToolboxBundle\Model\Document\Tag;

use Pimcore\Model;
use Symfony\Component\EventDispatcher\GenericEvent;

class DynamicLink extends Model\Document\Tag\Link
{
    /**
     * Return the type of the element
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
        $this->updatePathFromInternal();

        $url = $this->data['path'];

        if ($this->data['internalType'] == 'object') {
            $objectInfo = explode('::', $url);
            if (count($objectInfo) == 2) {

                $event = new GenericEvent($this, [
                    'className'         => $objectInfo[0],
                    'path'              => $objectInfo[1],
                    'objectFrontendUrl' => $url
                ]);

                \Pimcore::getEventDispatcher()->dispatch(
                    'toolbox.dynamiclink.object.url',
                        $event
                );

                $this->data['path'] = $event->getArgument('objectFrontendUrl');
            }
        }

        return parent::getHref();
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setDataFromEditmode($data)
    {
        parent::setDataFromEditmode($data);

        if (strpos($this->data['path'], '::') !== FALSE) {
            $this->data['internal'] = TRUE;
            $this->data['internalType'] = 'object';
        }

        return $this;
    }
}