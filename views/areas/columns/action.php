<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;
use Toolbox\Config;
use Toolbox\Tool\ElementBuilder;

class Columns extends Document\Tag\Area\AbstractArea
{
    /**
     *
     */
    public function action()
    {
        $adminData = NULL;
        if ($this->getView()->editmode) {
            $adminData = ElementBuilder::buildElementConfig('columns', $this->getView());
        }

        $equalHeight = $this->getView()->checkbox('equalHeight')->isChecked() && !$this->getView()->editmode;
        $type = $this->getView()->select('type')->getData();

        $partialName = '';
        $columns = [];

        $configNode = Config::getElementConfigElement('columns', 'type');

        if (!empty($type)) {

            $t = explode('_', $type);
            if ($this->getView()->toolboxHelper()->templateExists($this->getView(), 'toolbox/columns/' . $type . '.php')) {
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

        $this->getView()->assign([
            'adminData'   => $adminData,
            'type'        => $type,
            'columns'     => $columns,
            'partialName' => $partialName,
            'equalHeight' => $equalHeight,
        ]);
    }

    /**
     * @param $brick
     *
     * @return string
     */
    public function getBrickHtmlTagOpen($brick)
    {
        return '';
    }

    /**
     * @param $brick
     *
     * @return string
     */
    public function getBrickHtmlTagClose($brick)
    {
        return '';
    }
}