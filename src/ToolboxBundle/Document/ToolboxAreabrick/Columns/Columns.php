<?php

namespace ToolboxBundle\Document\ToolboxAreabrick\Columns;

use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Checkbox;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class Columns extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function __construct(
        private CalculatorRegistryInterface $calculatorRegistry,
        private EngineInterface $templating
    ) {
    }

    public function action(Info $info): ?Response
    {
        $this->buildInfoParameters($info);

        return parent::action($info);
    }

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->buildInfoParameters($info, $headlessResponse);

        parent::headlessAction($info, $headlessResponse);
    }

    private function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        $editMode = $info->getEditable()?->getEditmode() === true;

        /** @var Checkbox $equalHeightElement */
        $equalHeightElement = $this->getDocumentEditable($info->getDocument(), 'checkbox', 'equal_height');
        $typeElement = $this->getDocumentEditable($info->getDocument(), 'select', 'type');
        $gridAdjustment = $this->getDocumentEditable($info->getDocument(), 'columnadjuster', 'columnadjuster')->getData();

        $equalHeight = $equalHeightElement->isChecked() && $editMode === false;
        $type = $typeElement->getData();

        $partialName = '';

        $customColumnConfiguration = null;
        if (is_array($gridAdjustment) && count($gridAdjustment) > 0) {
            $customColumnConfiguration = [$type => $gridAdjustment];
        }

        $theme = $this->configManager->getConfig('theme');
        $columns = $this->calculatorRegistry
            ->getColumnCalculator($theme['calculators']['column_calculator'])
            ->calculateColumns($type, $customColumnConfiguration);

        if (!empty($columns)) {
            if ($this->templating->exists($this->getTemplatePath($type))) {
                $partialName = $type;
            } else {
                $t = explode('_', $type);
                $partialName = $t[0];
            }

            foreach ($columns as &$column) {
                $column['innerClass'] = 'toolbox-column' . ($equalHeight ? ' equal-height-item' : '');
            }
        }

        $info->setParam('columns', $columns);

        $brickParams = [
            'type'        => $type . ($customColumnConfiguration !== null ? '-grid-adjuster' : ''),
            'columns'     => $columns,
            'partialName' => $partialName,
            'equalHeight' => $equalHeight
        ];

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->setAdditionalConfigData($brickParams);

            return;
        }

        $info->setParams(array_merge($info->getParams(), $brickParams));
    }

    public function getName(): string
    {
        return 'Columns';
    }

    public function getDescription(): string
    {
        return 'Toolbox Grid Columns';
    }
}
