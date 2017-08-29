<?php

namespace ToolboxBundle\Document\Areabrick\Columns;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Columns extends AbstractAreabrick
{
    /**
     * @var ColumnCalculatorInterface
     */
    protected $calculator;

    /**
     * @param ColumnCalculatorInterface $calculator
     */
    public function setCalculator(ColumnCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();
        $equalHeightElement = $this->getDocumentTag($info->getDocument(),'checkbox', 'equalHeight');
        $typeElement = $this->getDocumentTag($info->getDocument(),'select', 'type');

        $editMode = $view->get('editmode');

        $equalHeight = $equalHeightElement->isChecked() && $editMode === FALSE;
        $type = $typeElement->getData();

        $partialName = '';

        $configNode = $this->getConfigManager()->getAreaElementConfig('columns', 'type');

        $columns = $this->calculator->calculateColumns($type, $configNode);

        if (!empty($columns)) {

            if ($this->container->get('templating')->exists('@Toolbox/Toolbox/Columns/' . $type . $this->getTemplateSuffix())) {
                $partialName = $type;
            } else {
                $t = explode('_', $type);
                $partialName = $t[0];
            }

            foreach ($columns as &$column) {
                $column['innerClass'] = 'toolbox-column' . ($equalHeight ? ' equal-height-item' : '');
            }
        }

        $view->type = $type;
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