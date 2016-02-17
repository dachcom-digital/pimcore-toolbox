<?php

namespace Toolbox\View\Helper;

class ToolboxHelper extends \Zend_View_Helper_Abstract {

    public function toolboxhelper() {

        return $this;

    }

    public function getAvailableBricks( $excludeBricks = array(), $extraBricks = array() ) {

        $excludeBricks = is_array( $excludeBricks ) ? $excludeBricks : [];
        $extraBricks = is_array( $extraBricks ) ? $extraBricks : [];

        $defaultBricks = \Toolbox\Config::getConfig()->allowedPlugins->toArray();

        $bricks = array_merge($extraBricks, array_keys($defaultBricks ));

        $params = array();

        foreach ($excludeBricks as $brick) {
            if (in_array($brick, $bricks)) {
                $bricks = array_diff($bricks, array($brick));
            }
        }

        foreach ($bricks as $brick) {
            $params[$brick] = array(
                "forceEditInView" => true
            );
        }

        return array('allowed' => $bricks, 'additional' => $params );

    }

    public function getAssetArray( $data ) {

        if( empty( $data ) ) {
            return array();
        }

        $assets = array();

        foreach ( $data as $element) {

            if ($element instanceof \Pimcore\Model\Asset\Image) {
                $assets[] = $element;
            } else if ($element instanceof \Pimcore\Model\Asset\Folder) {
                foreach ($element->getChilds() as $child) {
                    if ($child instanceof \Pimcore\Model\Asset\Image)
                        $assets[] = $child;
                }
            }

        }

        return $assets;

    }

    public function getConfigArray( $section = '', $createKeyValuePairs = FALSE ) {

        if( empty( $section ) )
            return array();

        $values = \Toolbox\Config::getConfig();
        $valueArray = $values->toArray();

        $store = array();

        if( isset( $valueArray[ $section ] ) && !empty( $valueArray[ $section ] ) ) {

            $store = $valueArray[ $section ];

        }

        if( $createKeyValuePairs && !empty( $store ) ) {

            $pairs = array();

            foreach( $store as $key => $value) {

                $pairs[] = array($key, $value);

            }

            return $pairs;

        }

        return $store;


    }

    public function getDefaultInputSettings( ) {

    }

}