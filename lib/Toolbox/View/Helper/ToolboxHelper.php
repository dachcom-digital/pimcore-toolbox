<?php

namespace Toolbox\View\Helper;

use Toolbox\Config;
use Pimcore\ExtensionManager;

class ToolboxHelper extends \Zend_View_Helper_Abstract {

    public function toolboxhelper()
    {
        return $this;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getAvailableBricks( $type = '' )
    {
        $areaElements = array_keys(ExtensionManager::getBrickConfigs());
        $disallowedSubAreas = Config::getConfig()->disallowedSubAreas->toArray();

        $bricks = [];

        $elementDisallowed = isset( $disallowedSubAreas[$type]) ? $disallowedSubAreas[$type] : array();

        foreach( $areaElements as $a )
        {
            if (!in_array($a, $elementDisallowed))
            {
                $bricks[] = $a;
            }
        }

        $params = array();

        foreach ($bricks as $brick)
        {
            $params[$brick] = array(
                'forceEditInView' => true
            );
        }

        return array('allowed' => $bricks, 'additional' => $params );

    }

    public function getAvailableSnippetBricks( )
    {
        $areaElements = array_keys(ExtensionManager::getBrickConfigs());
        $disallowedSubAreas = Config::getConfig()->disallowedContentSnippetAreas->toArray();

        $bricks = [];

        foreach( $areaElements as $a )
        {
            if (!in_array($a, $disallowedSubAreas))
            {
                $bricks[] = $a;
            }
        }

        $params = array();

        foreach ($bricks as $brick)
        {
            $params[$brick] = array(
                'forceEditInView' => true
            );
        }

        return array('allowed' => $bricks, 'additional' => $params );

    }

    /**
     * @param $data
     * @deprecated
     * @return array
     */
    public function getAssetArray( $data )
    {
        if( empty( $data ) )
        {
            return array();
        }

        $assets = array();

        foreach ( $data as $element)
        {
            if ($element instanceof \Pimcore\Model\Asset\Image)
            {
                $assets[] = $element;
            }
            else if ($element instanceof \Pimcore\Model\Asset\Folder)
            {
                foreach ($element->getChilds() as $child)
                {
                    if ($child instanceof \Pimcore\Model\Asset\Image)
                        $assets[] = $child;
                }
            }

        }

        return $assets;

    }


}