<?php

namespace Toolbox\Tools;

use Toolbox\Config;
use Pimcore\ExtensionManager;

class Area {

    /**
     * @param null $type
     * @param bool $fromSnippet
     *
     * @return array
     */
    public static function getAreaBlockConfiguration( $type = NULL, $fromSnippet = FALSE )
    {
        if( $fromSnippet === TRUE)
        {
            $availableBricks = self::getAvailableBricksForSnippets();
        }
        else
        {
            $availableBricks = self::getAvailableBricks( $type );
        }

        $areaBlockConfiguration = Config::getConfig()->areaBlockConfiguration;
        $areaBlockConfigurationArray = is_null( $areaBlockConfiguration ) ? [] : $areaBlockConfiguration->toArray();

        $configuration = [];

        $configuration['params'] = $availableBricks['params'];
        $configuration['allowed'] = $availableBricks['allowed'];

        if( isset($areaBlockConfigurationArray['groups']) && $areaBlockConfigurationArray['groups'] !== FALSE)
        {
            $toolboxGroup = array(
                array(
                    'name' => 'Toolbox',
                    'elements' => self::getToolboxBricks()
                )
            );

            $groups = array_merge($toolboxGroup, $areaBlockConfigurationArray['groups']);

            $cleanedGroups = [];
            $cleanedGroupsSorted = [];

            foreach($groups as $groupName => $groupData)
            {
                $groupName = $groupData['name'];
                $cleanedGroup = [];

                foreach($groupData['elements'] as $element )
                {
                    if( in_array($element, $availableBricks['allowed']))
                    {
                        $cleanedGroup[] = $element;
                    }

                }

                //ok, group elements found, add them
                if( count($cleanedGroup) > 0)
                {
                    $cleanedGroups[ $groupName ] = $cleanedGroup;
                    $cleanedGroupsSorted = array_merge( $cleanedGroupsSorted, $cleanedGroup);
                }
            }

            if( count($cleanedGroups) > 0)
            {
                $configuration[ 'sorting' ] = $cleanedGroupsSorted;
                $configuration[ 'group' ] = $cleanedGroups;
            }
        }

        if( isset($areaBlockConfigurationArray['toolbar']) && is_array($areaBlockConfigurationArray['toolbar']))
        {
            $configuration['areablock_toolbar'] = $areaBlockConfigurationArray['toolbar'];
        }

        return $configuration;

    }

    /**
     * @param bool $arrayKeys
     *
     * @return array|mixed
     */
    private static function getActiveBricks($arrayKeys = TRUE)
    {
        $areaElements = ExtensionManager::getBrickConfigs();

        /**
         * @var String $areaElementName
         * @var \Zend_Config_Xml $areaElementData
         */
        foreach($areaElements as $areaElementName => $areaElementData)
        {
            if(!ExtensionManager::isEnabled('brick', $areaElementName))
            {
                unset($areaElements[ $areaElementName ]);
                continue;
            }
        }

        if( $arrayKeys === TRUE )
        {
            return array_keys($areaElements);
        }

        return $areaElements;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private static function getAvailableBricks( $type = NULL )
    {
        $areaElements = self::getActiveBricks();
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
                'forceEditInView' => TRUE
            );
        }

        return array('allowed' => $bricks, 'params' => $params );

    }

    /**
     * @return array
     */
    private static function getAvailableBricksForSnippets()
    {
        $areaElements = self::getActiveBricks();
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
                'forceEditInView' => TRUE
            );
        }

        return array('allowed' => $bricks, 'params' => $params );

    }

    /**
     * @return array
     */
    private static function getToolboxBricks()
    {
        $areaElements = self::getActiveBricks(FALSE);
        $toolboxBricks = [];

        /**
         * @var String $areaElementName
         * @var \Zend_Config_Xml $areaElementData
         */
        foreach($areaElements as $areaElementName => $areaElementData)
        {
            $data = $areaElementData->toArray();

            if( substr($data['description'], 0, 7) === 'Toolbox')
            {
                $toolboxBricks[ $areaElementName ] = $areaElementData;
            }

        }

        if( isset( $toolboxBricks['content'] ))
        {
            $toolboxBricks = array('content' => $toolboxBricks['content'] ) + $toolboxBricks;
        }

        if( isset( $toolboxBricks['headline'] ))
        {
            $toolboxBricks = array('headline' => $toolboxBricks['headline'] ) + $toolboxBricks;
        }

        return array_keys($toolboxBricks);
    }
}