<?php

namespace ToolboxBundle\Manager;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Extension\Document\Areabrick\AreabrickManager;

class AreaManager implements AreaManagerInterface
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected AreabrickManager $brickManager,
        protected PermissionManagerInterface $permissionManager
    ) {
    }

    public function getAreaBlockName(?string $type = null): string
    {
        if ($type === 'parallaxContainerSection') {
            return 'Parallax Container Section';
        }

        return $this->brickManager->getBrick($type)->getName();
    }

    public function getAreaBlockConfiguration(?string $type, bool $fromSnippet = false, bool $editMode = false): array
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

        foreach ($groups as $groupData) {
            $groupName = $groupData['name'];
            $cleanedGroup = [];

            $sorting = $groupData['sorting'] ?? self::BRICK_GROUP_SORTING_ALPHABETICALLY;

            foreach ($groupData['elements'] as $element) {
                if (in_array($element, $availableBricks['allowed'], true)) {
                    $cleanedGroup[] = $element;
                }
            }

            if (count($cleanedGroup) > 0) {
                $cleanedGroups[$groupName] = $cleanedGroup;

                if ($sorting === self::BRICK_GROUP_SORTING_ALPHABETICALLY) {
                    sort($cleanedGroup);
                }

                $cleanedGroupsSorted[] = $cleanedGroup;
            }
        }

        if (count($cleanedGroups) > 0) {
            $configuration['sorting'] = array_merge([], ...$cleanedGroupsSorted);
            $configuration['group'] = $cleanedGroups;
        }

        $configuration['controlsAlign'] = $areaBlockConfigurationArray['controlsAlign'];
        $configuration['controlsTrigger'] = $areaBlockConfigurationArray['controlsTrigger'];
        $configuration['areablock_toolbar'] = $areaBlockConfigurationArray['toolbar'];

        $configuration['toolbox_permissions'] = [
            'disallowed' => $editMode ? $this->permissionManager->getDisallowedEditables($configuration['allowed']) : []
        ];

        return $configuration;
    }

    /**
     * @throws \Exception
     */
    private function getActiveBricks(bool $arrayKeys = true): array
    {
        $areaElements = $this->brickManager->getBricks();

        //sort area elements by key => area name
        ksort($areaElements);

        /** @var AbstractTemplateAreabrick $areaElementData */
        foreach ($areaElements as $areaElementName => $areaElementData) {
            if (!$this->configManager->areaIsEnabled($areaElementName)) {
                unset($areaElements[$areaElementName]);
            }
        }

        //if in context, check if areas are available in given context
        if ($this->configManager->isContextConfig()) {
            $contextConfiguration = $this->configManager->getCurrentContextSettings();

            if ($contextConfiguration['merge_with_root'] === true) {
                if (!empty($contextConfiguration['enabled_areas'])) {
                    foreach ($areaElements as $areaElementName => $areaElementData) {
                        if (!in_array($areaElementName, $contextConfiguration['enabled_areas'], true)) {
                            unset($areaElements[$areaElementName]);
                        }
                    }
                } elseif (!empty($contextConfiguration['disabled_areas'])) {
                    foreach ($areaElements as $areaElementName => $areaElementData) {
                        if (in_array($areaElementName, $contextConfiguration['disabled_areas'], true)) {
                            unset($areaElements[$areaElementName]);
                        }
                    }
                }
            } else {
                foreach ($areaElements as $areaElementName => $areaElementData) {
                    $areas = $this->configManager->getConfig('areas');
                    if (!array_key_exists($areaElementName, $areas)) {
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
     * @throws \Exception
     */
    private function getAvailableBricks(string $type): array
    {
        $areaElements = $this->getActiveBricks();

        $areaAppearance = $this->configManager->getConfig('areablock_restriction');
        $elementAllowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['allowed'] : [];
        $elementDisallowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['disallowed'] : [];

        // strict fill means: only add defined elements.
        $strictFill = !empty($elementAllowed);

        $bricks = [];
        foreach ($areaElements as $a) {
            // allowed rule comes first!
            if ($strictFill === true) {
                if (in_array($a, $elementAllowed, true)) {
                    $bricks[] = $a;
                }
            } else {
                if (!in_array($a, $elementDisallowed, true)) {
                    $bricks[] = $a;
                }
            }
        }

        return ['allowed' => $bricks, 'params' => []];
    }

    /**
     * @throws \Exception
     */
    private function getAvailableBricksForSnippets(string $type): array
    {
        $areaElements = $this->getActiveBricks();

        $areaAppearance = $this->configManager->getConfig('snippet_areablock_restriction');
        $elementAllowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['allowed'] : [];
        $elementDisallowed = isset($areaAppearance[$type]) ? $areaAppearance[$type]['disallowed'] : [];

        $bricks = [];
        foreach ($areaElements as $a) {
            // allowed rule comes first!
            if (!empty($elementAllowed)) {
                if (in_array($a, $elementAllowed, true)) {
                    $bricks[] = $a;
                }
            } else {
                if (!in_array($a, $elementDisallowed, true)) {
                    $bricks[] = $a;
                }
            }
        }

        return ['allowed' => $bricks, 'params' => []];
    }

    /**
     * @throws \Exception
     */
    private function getToolboxBricks(): array
    {
        $areaElements = $this->getActiveBricks(false);
        $toolboxBricks = [];

        /** @var AbstractTemplateAreabrick $areaElementData */
        foreach ($areaElements as $areaElementName => $areaElementData) {
            if (str_starts_with($areaElementData->getDescription(), 'Toolbox')) {
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
