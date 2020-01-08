<?php

namespace ToolboxBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class ColumnAdjusterController extends Controller\AdminController
{
    /**
     * @var CalculatorRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @var ConfigManagerInterface
     */
    private $configManager;

    /**
     * @param ConfigManagerInterface      $configManager
     * @param CalculatorRegistryInterface $calculatorRegistry
     */
    public function __construct(
        ConfigManagerInterface $configManager,
        CalculatorRegistryInterface $calculatorRegistry
    ) {
        $this->configManager = $configManager;
        $this->calculatorRegistry = $calculatorRegistry;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getColumnInfoAction(Request $request)
    {
        $currentColumn = $request->get('currentColumn');
        $customColumnConfigurationData = $request->get('customColumnConfiguration');

        $response = json_decode($customColumnConfigurationData, true);
        $customColumnConfiguration = null;

        if (is_array($response)) {
            $customColumnConfiguration = [$currentColumn => $response];
        }

        $theme = $this->configManager->getConfig('theme');
        $gridSize = isset($theme['grid']) && isset($theme['grid']['grid_size']) ? $theme['grid']['grid_size'] : null;
        $columnCalculator = $this->calculatorRegistry->getColumnCalculator($theme['calculators']['column_calculator']);
        $breakPointConfiguration = $columnCalculator->getColumnInfoForAdjuster($currentColumn, $customColumnConfiguration);

        $columnStore = isset($theme['grid']) && isset($theme['grid']['column_store']) ? $theme['grid']['column_store'] : null;
        $layout = isset($theme['layout']) ? strtolower($theme['layout']) : null;

        return $this->json(['breakPoints' => $breakPointConfiguration, 'gridSize' => $gridSize, 'columnStore' => $columnStore, 'layout' => $layout]);
    }
}
