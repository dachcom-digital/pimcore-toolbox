<?php

namespace ToolboxBundle\Manager;

use Pimcore\Extension\Document\Areabrick\AreabrickManager;

class AreaManager implements AreaManagerInterface
{
    /**
     * @var ConfigManagerInterface
     */
    public $configManager;

    /**
     * @var AreabrickManager
     */
    public $brickManager;

    /**
     * @param ConfigManagerInterface $configManager
     * @param AreabrickManager       $brickManager
     */
    public function __construct(ConfigManagerInterface $configManager, AreabrickManager $brickManager)
    {
        $this->configManager = $configManager;
        $this->brickManager = $brickManager;
    }

    /**
     * @param null $type
     *
     * @return string
     */
    public function getAreaBlockName($type = null)
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
     *
     * @throws \Exception
     */
    public function getAreaBlockConfiguration($type = null, $fromSnippet = false)
    {
        if ($fromSnippet === true) {
            $availableBricks = $this->getAvailableBricksForSnippets($type);
        } else {
            $availableBricks = $this->getAvailableBricks($type);
        }

        $areaBlockConfiguration = $this->configManager->getConfig('area_block_configuration');
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

        if (isset($areaBlockConfigurationArray['groups']) && $areaBlockConfigurationArray['groups'] !== false) {
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
                //sort group by cleaned group
                sort($cleanedGroupsSorted);
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
     * @return array
     *
     * @throws \Exception
     */
    private function getActiveBricks($arrayKeys = true)
    {
        $areaElements = $this->brickManager->getBricks();

        //sort area elements by key => area name
        ksort($areaElements);

        /** @var \Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick $areaElementData */
        foreach ($areaElements as $areaElementName => $areaElementData) {
            if (!$this->brickManager->isEnabled($areaElementName)) {
                unset($areaElements[$areaElementName]);
            }
        }

        //if in context, check if areas are available in given context
        if ($this->configManager->isContextConfig()) {
            $contextConfiguration = $this->configManager->getCurrentContextSettings();

            if ($contextConfiguration['merge_with_root'] === true) {
                if (!empty($contextConfiguration['enabled_areas'])) {
                    foreach ($areaElements as $areaElementName => $areaElementData) {
                        if (!in_array($areaElementName, $contextConfiguration['enabled_areas'])) {
                            unset($areaElements[$areaElementName]);
                        }
                    }
                } elseif (!empty($contextConfiguration['disabled_areas'])) {
                    foreach ($areaElements as $areaElementName => $areaElementData) {
                        if (in_array($areaElementName, $contextConfiguration['disabled_areas'])) {
                            unset($areaElements[$areaElementName]);
                        }
                    }
                }
            } else {
                foreach ($areaElements as $areaElementName => $areaElementData) {
                    $coreAreas = $this->configManager->getConfig('areas');
                    $customAreas = $this->configManager->getConfig('custom_areas');
                    if (!in_array($areaElementName, array_keys($coreAreas)) &&
                        !in_array($areaElementName, array_keys($customAreas))) {
                        unset($areaElements[$areaElementName]);
                    }
                }
            }
        }

        if ($arrayKeys === true) {
            return array_keys($areaElements);
        }

        return $areaElements;
    }

    /**
     * @param string $type
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getAvailableBricks($type = null)
    {
        $areaElements = $this->getActiveBricks();

        // @deprecated: remove in 4.0
        $disallowedSubAreas = $this->configManager->getConfig('disallowed_subareas');
        $depElementDisallowed = isset($disallowedSubAreas[$type]) ? $disallowedSubAreas[$type]['disallowed'] : [];

        $areaAppearance = $this->configManager->getConfig('areas_appearance');
        $elementAllowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['allowed'] : [];
        $elementDisallowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['disallowed'] : [];

        // strict fill means: only add defined elements.
        $strictFill = !empty($elementAllowed);

        // merge disallowed with deprecated disallowed
        $elementDisallowed = array_merge($elementDisallowed, $depElementDisallowed);

        $bricks = [];
        foreach ($areaElements as $a) {
            // allowed rule comes first!
            if ($strictFill === true) {
                if (in_array($a, $elementAllowed)) {
                    $bricks[] = $a;
                }
            } else {
                if (!in_array($a, $elementDisallowed)) {
                    $bricks[] = $a;
                }
            }
        }

        $params = [];
        foreach ($bricks as $brick) {
            $params[$brick] = [
                'forceEditInView' => true
            ];
        }

        return ['allowed' => $bricks, 'params' => $params];
    }

    /**
     * @param string $type
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getAvailableBricksForSnippets($type)
    {
        $areaElements = $this->getActiveBricks();

        // @deprecated: remove in 4.0
        $disallowedSubAreas = $this->configManager->getConfig('disallowed_content_snippet_areas');

        $areaAppearance = $this->configManager->getConfig('snippet_areas_appearance');
        $elementAllowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['allowed'] : [];
        $elementDisallowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['disallowed'] : [];

        // merge disallowed with deprecated disallowed
        $elementDisallowed = array_merge($elementDisallowed, is_array($disallowedSubAreas) ? $disallowedSubAreas : []);

        $bricks = [];
        foreach ($areaElements as $a) {
            // allowed rule comes first!
            if (!empty($elementAllowed)) {
                if (in_array($a, $elementAllowed)) {
                    $bricks[] = $a;
                }
            } else {
                if (!in_array($a, $elementDisallowed)) {
                    $bricks[] = $a;
                }
            }
        }

        $params = [];
        foreach ($bricks as $brick) {
            $params[$brick] = [
                'forceEditInView' => true
            ];
        }

        return ['allowed' => $bricks, 'params' => $params];
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    private function getToolboxBricks()
    {
        $areaElements = $this->getActiveBricks(false);
        $toolboxBricks = [];

        /** @var \Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick $areaElementData */
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
