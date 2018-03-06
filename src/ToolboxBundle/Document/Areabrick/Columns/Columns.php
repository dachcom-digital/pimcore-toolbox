<?php

namespace ToolboxBundle\Document\Areabrick\Columns;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class Columns extends AbstractAreabrick
{
    /**
     * @var CalculatorRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @param CalculatorRegistryInterface $calculatorRegistry
     */
    public function __construct(CalculatorRegistryInterface $calculatorRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    /**
     * @param Info $info
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();
        $editMode = $view->get('editmode');

        $equalHeightElement = $this->getDocumentTag($info->getDocument(), 'checkbox', 'equal_height');
        $typeElement = $this->getDocumentTag($info->getDocument(), 'select', 'type');
        $gridAdjustment = $this->getDocumentTag($info->getDocument(), 'columnadjuster', 'columnadjuster')->getData();

        $equalHeight = $equalHeightElement->isChecked() && $editMode === FALSE;
        $type = $typeElement->getData();

        $partialName = '';

        $customColumnConfiguration = NULL;
        if ($gridAdjustment !== FALSE) {
            $customColumnConfiguration = [$type => $gridAdjustment];
        }

        $theme = $this->configManager->getConfig('theme');
        $columns = $this->calculatorRegistry->getColumnCalculator($theme['calculators']['column_calculator'])->calculateColumns($type, $customColumnConfiguration);

        if (!empty($columns)) {

            if ($this->container->get('templating')->exists($this->getTemplatePath($type))) {
                $partialName = $type;
            } else {
                $t = explode('_', $type);
                $partialName = $t[0];
            }

            foreach ($columns as &$column) {
                $column['innerClass'] = 'toolbox-column' . ($equalHeight ? ' equal-height-item' : '');
            }
        }

        $view->type = $type . ($gridAdjustment !== FALSE ? '-grid-adjuster' : '');
        $view->columns = $columns;
        $view->partialName = $partialName;
        $view->equalHeight = $equalHeight;

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