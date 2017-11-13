<?php

namespace ToolboxBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Service\ConfigManager;

class ColumnAdjusterController extends Controller\AdminController
{
    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getColumnInfoAction(Request $request)
    {
        $columnCalculator = $this->get(ColumnCalculatorInterface::class);
        $configManager = $this->get(ConfigManager::class);

        $currentColumn = $request->request->get('currentColumn');

        $configManager->setAreaNameSpace(ConfigManager::AREABRICK_NAMESPACE_INTERNAL);
        $configNode = $configManager->getAreaElementConfig('columns', 'type');
        $themeSettings = $configManager->getConfig('theme');

        $columnConfigElements = isset($configNode['config']['store']) ? $configNode['config']['store'] : [];
        $breakPointConfiguration = $columnCalculator->getColumnInfoForAdjuster($currentColumn, $columnConfigElements, $themeSettings['grid']);

        return $this->json(['breakPoints' => $breakPointConfiguration]);
    }
}