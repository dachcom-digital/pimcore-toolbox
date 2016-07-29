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
     * @param string|array $areaType toolbox element or custom config
     * @param null|object $element related element to track
     *
     * @return string
     */
    public function addTracker( $areaType, $element = NULL)
    {
        if( empty( $areaType ) )
        {
            return '';
        }

        if( is_array( $areaType ) )  //custom data
        {
            $trackerInfo = $areaType;
        }
        else //area data
        {
            $configNode = Config::getConfig()->{$areaType};

            if(empty($configNode))
            {
                return '';
            }

            $configInfo = $configNode->toArray();

            if( !isset($configInfo['eventTracker']))
            {
                return '';
            }

            $trackerInfo = $configInfo['eventTracker'];

        }

        $str = 'data-tracking="active" ';

        $str .= join(' ', array_map(function($key) use ($trackerInfo, $element)
        {
            $val = $trackerInfo[$key];

            if ( is_bool($val) )
            {
                $val = (int) $val;
            }

            if( $key === 'label' && is_array($val))
            {
                //userfunc. 0 => (string) method, 1 = (array) arguments
                $getter = $val;
                $val = call_user_func_array( array($element, $getter[0]), $getter[1] );

                if( empty($val) )
                {
                    $val = 'no label given';
                }
            }

            return 'data-' . $key . '="' . $val . '"';

        }, array_keys( $trackerInfo ) ) );

        return $str;
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