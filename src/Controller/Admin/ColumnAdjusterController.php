<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class ColumnAdjusterController extends AdminAbstractController
{
    public function __construct(
        private ConfigManagerInterface $configManager,
        private CalculatorRegistryInterface $calculatorRegistry
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getColumnInfoAction(Request $request): JsonResponse
    {
        $currentColumn = $request->get('currentColumn');
        $customColumnConfigurationData = $request->get('customColumnConfiguration');

        $response = json_decode($customColumnConfigurationData, true);
        $customColumnConfiguration = null;

        if (is_array($response)) {
            $customColumnConfiguration = [$currentColumn => $response];
        }

        $theme = $this->configManager->getConfig('theme');
        $layout = isset($theme['layout']) ? strtolower($theme['layout']) : null;

        $columnCalculator = $this->calculatorRegistry->getColumnCalculator($theme['calculators']['column_calculator']);
        $breakPointConfiguration = $columnCalculator->getColumnInfoForAdjuster($currentColumn, $customColumnConfiguration);

        $gridSize = $this->assertGridConfig($theme, 'grid_size', 'integer');
        $columnStore = $this->assertGridConfig($theme, 'column_store', 'array');

        if (is_array($columnStore) && count($columnStore) === 0) {
            $columnStore = null;
        }

        return $this->json([
            'breakPoints' => $breakPointConfiguration,
            'gridSize'    => $gridSize,
            'columnStore' => $columnStore,
            'layout'      => $layout
        ]);
    }

    protected function assertGridConfig(array $theme, string $node, string $expectedType = 'string'): mixed
    {
        if (!isset($theme['grid'])) {
            return null;
        }

        if (!isset($theme['grid'][$node])) {
            return null;
        }

        if (gettype($theme['grid'][$node]) !== $expectedType) {
            return null;
        }

        return $theme['grid'][$node];
    }
}
