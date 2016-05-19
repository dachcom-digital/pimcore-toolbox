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

    /**
     * @param string $section
     * @param bool   $createKeyValuePairs
     * @param bool   $addDefault
     *
     * @deprecated
     * @return array
     */
    public function getConfigArray( $section = '', $createKeyValuePairs = FALSE, $addDefault = FALSE )
    {
        if( empty( $section ) )
            return array();

        $values = \Toolbox\Config::getConfig();

        $sectionPaths = explode('/', $section );

        $data = $values;

        foreach( $sectionPaths as $sectionPath)
        {
            $data = $data->{$sectionPath};
        }

        $sectionDataArray = array();

        if( !empty( $data ) )
        {
            $sectionDataArray = $data->toArray();
        }

        if ( $addDefault && count($sectionDataArray) > 0 ) {
            $sectionDataArray = array('default' => 'Standard') + $sectionDataArray;
        }

        if( $createKeyValuePairs && !empty( $sectionDataArray ) )
        {
            $pairs = array();

            foreach( $sectionDataArray as $key => $value)
            {
                $pairs[] = array($key, $value);
            }

            return $pairs;

        }

        return $sectionDataArray;

    }

}