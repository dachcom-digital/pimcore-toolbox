<?php

namespace ToolboxBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use ToolboxBundle\Calculator\ColumnCalculatorInterface;

class ColumnAdjusterController extends Controller\AdminController
{
    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     */
    public function getColumnInfoAction(Request $request)
    {
        $currentColumn = $request->request->get('currentColumn');
        $customColumnConfigurationData = $request->request->get('customColumnConfiguration');

        $response = json_decode($customColumnConfigurationData, TRUE);
        $customColumnConfiguration = NULL;
        if(is_array($response)) {
            $customColumnConfiguration = [$currentColumn => $response];
        }

        $columnCalculator = $this->get(ColumnCalculatorInterface::class);
        $breakPointConfiguration = $columnCalculator->getColumnInfoForAdjuster($currentColumn, $customColumnConfiguration);

        return $this->json(['breakPoints' => $breakPointConfiguration]);
    }
}