<?php

namespace ToolboxBundle\Document\Areabrick\Columns;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class Columns extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();

        $elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);

        $equalHeightElement = $this->getDocumentTag($info->getDocument(),'checkbox', 'equalHeight');
        $typeElement = $this->getDocumentTag($info->getDocument(),'select', 'type');

        $editMode = $view->get('editmode');

        $equalHeight = $equalHeightElement->isChecked() && $editMode === FALSE;
        $type = $typeElement->getData();

        $partialName = '';
        $columns = [];

        $configNode = $this->getConfigManager()->getAreaElementConfig('columns', 'type');

        if (!empty($type)) {

            $t = explode('_', $type);
            if ($this->container->get('templating')->exists('@Toolbox/Toolbox/Columns/' . $type . $this->getTemplateSuffix())) {
                $partialName = $type;
            } else {
                $partialName = $t[0];
            }

            //remove "column" in string.
            $_columns = array_splice($t, 1);

            $bootstrapOffsetConfig = [];

            foreach ($_columns as $i => $columnClass) {

                $bootstrapClassConfig = [
                    'xs' => 'col-xs-12',
                    'sm' => 'col-sm-' . $columnClass,
                    'md' => 'col-md-' . $columnClass
                ];

                //config is an array, use special breakpoint classes!
                if (isset($configNode['values'][$type]) && isset($configNode['values'][$type]['breakpoints'])) {

                    $customBreakPoints = $configNode['values'][$type]['breakpoints'];
                    foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                        $customBreakPointDataColumns = explode('_', $customBreakPointData);
                        $customColAmount = isset($customBreakPointDataColumns[$i]) ? $customBreakPointDataColumns[$i] : 12;
                        $bootstrapClassConfig[$customBreakPointName] = 'col-' . $customBreakPointName . '-' . $customColAmount;
                    }
                }

                if (substr($columnClass, 0, 1) === 'o') {

                    $offset = (int)substr($columnClass, 1);

                    $bootstrapOffsetConfig = [
                        'sm' => 'col-sm-offset-' . $offset,
                        'md' => 'col-md-offset-' . $offset
                    ];

                    //config is an array, use special breakpoint classes!
                    if (isset($configNode['values'][$type]) && isset($configNode['values'][$type]['breakpoints'])) {

                        $customBreakPoints = $configNode['values'][$type]['breakpoints'];
                        foreach ($customBreakPoints as $customBreakPointName => $customBreakPointData) {
                            $customBreakPointDataColumns = explode('_', $customBreakPointData);
                            $customColAmount = isset($customBreakPointDataColumns[$i]) ? $customBreakPointDataColumns[$i] : 12;

                            if (substr($customColAmount, 0, 1) === 'o') {
                                $customOffset = (int)substr($customColAmount, 1);
                                $bootstrapOffsetConfig[$customBreakPointName] = 'col-' . $customBreakPointName . '-offset-' . $customOffset;
                            }
                        }
                    }

                    //skip column because of offset column.
                    continue;
                }

                $columns[] = [
                    'offset'      => join(' ', $bootstrapOffsetConfig),
                    'btClass'     => join(' ', $bootstrapClassConfig),
                    'columnType'  => $type,
                    'columnClass' => 'toolbox-column' . ($equalHeight ? ' equal-height-item' : ''),
                    'name'        => 'column_' . $i
                ];

                $bootstrapOffsetConfig = [];
            }
        }

        $view->elementConfigBar = $elementConfigBar;
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