<?php

namespace ToolboxBundle\Service;

use Pimcore\Extension\Document\Areabrick\AreabrickManager;

class AreaManager
{
    /**
     * @var ConfigManager
     */
    var $configManager;

    /**
     * @var AreabrickManager
     */
    var $brickManager;

    /**
     * ElementBuilder constructor.
     *
     * @param ConfigManager    $configManager
     * @param AreabrickManager $brickManager
     */
    public function __construct(ConfigManager $configManager, AreabrickManager $brickManager)
    {
        $this->configManager = $configManager;
        $this->brickManager = $brickManager;
    }

    public function getAreaBlockName($type = NULL)
    {
        if ($type === 'parallaxContainerSection') {
            return 'Parallax Container Section';
        }

        return $this->brickManager->getBrick($type)->getName();
    }

    /**
     * @param null $type
     * @param bool $fromSnippet
     *
     * @return array
     */
    public function getAreaBlockConfiguration($type = NULL, $fromSnippet = FALSE)
    {
        if ($fromSnippet === TRUE) {
            $availableBricks = $this->getAvailableBricksForSnippets();
        } else {
            $availableBricks = $this->getAvailableBricks($type);
        }

        $areaBlockConfiguration = $this->configManager->getConfig('areaBlockConfiguration');
        $areaBlockConfigurationArray = is_null($areaBlockConfiguration) ? [] : $areaBlockConfiguration;

        $configuration = [];

        $configuration['params'] = $availableBricks['params'];
        $configuration['allowed'] = $availableBricks['allowed'];

        $toolboxGroup = [
            [
                'name'     => 'Toolbox',
                'elements' => $this->getToolboxBricks()
            ]
        ];

        if (isset($areaBlockConfigurationArray['groups']) && $areaBlockConfigurationArray['groups'] !== FALSE) {
            $groups = array_merge($toolboxGroup, $areaBlockConfigurationArray['groups']);
        } else {
            $groups = $toolboxGroup;
        }

        $cleanedGroups = [];
        $cleanedGroupsSorted = [];

        foreach ($groups as $groupName => $groupData) {
            $groupName = $groupData['name'];
            $cleanedGroup = [];

            foreach ($groupData['elements'] as $element) {
                if (in_array($element, $availableBricks['allowed'])) {
                    $cleanedGroup[] = $element;
                }
            }

            //ok, group elements found, add them
            if (count($cleanedGroup) > 0) {
                $cleanedGroups[$groupName] = $cleanedGroup;
                $cleanedGroupsSorted = array_merge($cleanedGroupsSorted, $cleanedGroup);
            }
        }

        if (count($cleanedGroups) > 0) {
            $configuration['sorting'] = $cleanedGroupsSorted;
            $configuration['group'] = $cleanedGroups;
        }


        if (isset($areaBlockConfigurationArray['toolbar']) && is_array($areaBlockConfigurationArray['toolbar'])) {
            $configuration['areablock_toolbar'] = $areaBlockConfigurationArray['toolbar'];
        }

        return $configuration;
    }

    /**
     * @param bool $arrayKeys
     *
     * @return array|mixed
     */
    private function getActiveBricks($arrayKeys = TRUE)
    {
        $areaElements = $this->brickManager->getBricks();

        /**
         * @var \Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick $areaElementData
         */
        foreach ($areaElements as $areaElementName => $areaElementData) {
            if (!$this->brickManager->isEnabled($areaElementName)) {
                unset($areaElements[$areaElementName]);
                continue;
            }
        }

        if ($arrayKeys === TRUE) {
            return array_keys($areaElements);
        }

        return $areaElements;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private function getAvailableBricks($type = NULL)
    {
        $areaElements = $this->getActiveBricks();
        $disallowedSubAreas = $this->configManager->getConfig('disallowedSubAreas');

        $bricks = [];

        $elementDisallowed = isset($disallowedSubAreas[$type]) ? $disallowedSubAreas[$type]['disallowed'] : [];

        foreach ($areaElements as $a) {
            if (!in_array($a, $elementDisallowed)) {
                $bricks[] = $a;
            }
        }

        $params = [];

        foreach ($bricks as $brick) {
            $params[$brick] = [
                'forceEditInView' => TRUE
            ];
        }

        return ['allowed' => $bricks, 'params' => $params];
    }

    /**
     * @return array
     */
    private function getAvailableBricksForSnippets()
    {
        $areaElements = $this->getActiveBricks();
        $disallowedSubAreas = $this->configManager->getConfig('disallowedContentSnippetAreas');

        $bricks = [];

        foreach ($areaElements as $a) {
            if (!in_array($a, $disallowedSubAreas)) {
                $bricks[] = $a;
            }
        }

        $params = [];

        foreach ($bricks as $brick) {
            $params[$brick] = [
                'forceEditInView' => TRUE
            ];
        }

        return ['allowed' => $bricks, 'params' => $params];
    }

    /**
     * @return array
     */
    private function getToolboxBricks()
    {
        $areaElements = $this->getActiveBricks(FALSE);
        $toolboxBricks = [];

        /**
         * @var String                                                          $areaElementName
         * @var \Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick $areaElementData
         */
        foreach ($areaElements as $areaElementName => $areaElementData) {
            if (substr($areaElementData->getDescription(), 0, 7) === 'Toolbox') {
                $toolboxBricks[$areaElementName] = $areaElementData;
            }
        }

        if (isset($toolboxBricks['content'])) {
            $toolboxBricks = ['content' => $toolboxBricks['content']] + $toolboxBricks;
        }

        if (isset($toolboxBricks['headline'])) {
            $toolboxBricks = ['headline' => $toolboxBricks['headline']] + $toolboxBricks;
        }

        return array_keys($toolboxBricks);
    }
}