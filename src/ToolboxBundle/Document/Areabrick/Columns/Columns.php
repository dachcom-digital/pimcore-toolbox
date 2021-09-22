<?php

namespace ToolboxBundle\Document\Areabrick\Columns;

use Pimcore\Model\Document\Editable\Checkbox;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class Columns extends AbstractAreabrick
{
    private CalculatorRegistryInterface $calculatorRegistry;
    private EngineInterface $templating;

    public function __construct(
        CalculatorRegistryInterface $calculatorRegistry,
        EngineInterface $templating
    ) {
        $this->calculatorRegistry = $calculatorRegistry;
        $this->templating = $templating;
    }

    public function action(Info $info): ?Response
    {
        parent::action($info);

        $editMode = $info->getParam('editmode');

        /** @var Checkbox $equalHeightElement */
        $equalHeightElement = $this->getDocumentEditable($info->getDocument(), 'checkbox', 'equal_height');
        $typeElement = $this->getDocumentEditable($info->getDocument(), 'select', 'type');
        $gridAdjustment = $this->getDocumentEditable($info->getDocument(), 'columnadjuster', 'columnadjuster')->getData();

        $equalHeight = $equalHeightElement->isChecked() && $editMode === false;
        $type = $typeElement->getData();

        $partialName = '';

        $customColumnConfiguration = null;
        if ($gridAdjustment !== false) {
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

        $info->setParams(array_merge($info->getParams(), [
            'type'        => $type . ($gridAdjustment !== false ? '-grid-adjuster' : ''),
            'columns'     => $columns,
            'partialName' => $partialName,
            'equalHeight' => $equalHeight
        ]));

        return null;
    }

    public function getName()
    {
        return 'Columns';
    }

    public function getDescription()
    {
        return 'Toolbox Grid Columns';
    }
}
