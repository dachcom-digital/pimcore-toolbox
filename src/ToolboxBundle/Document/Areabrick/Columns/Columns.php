<?php

namespace ToolboxBundle\Document\Areabrick\Columns;

use Symfony\Component\Templating\EngineInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class Columns extends AbstractAreabrick
{
    private CalculatorRegistryInterface $calculatorRegistry;

    public function __construct(CalculatorRegistryInterface $calculatorRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    public function action(Info $info)
    {
        parent::action($info);

        $editMode = $info->getParam('editmode');

        /** @var \Pimcore\Model\Document\Editable\Checkbox $equalHeightElement */
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
            if ($this->container->get(EngineInterface::class)->exists($this->getTemplatePath($type))) {
                $partialName = $type;
            } else {
                $t = explode('_', $type);
                $partialName = $t[0];
            }

            foreach ($columns as &$column) {
                $column['innerClass'] = 'toolbox-column' . ($equalHeight ? ' equal-height-item' : '');
            }
        }

        $info->setParams([
            'type'        => $type . ($gridAdjustment !== false ? '-grid-adjuster' : ''),
            'columns'     => $columns,
            'partialName' => $partialName,
            'equalHeight' => $equalHeight
        ]);
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
