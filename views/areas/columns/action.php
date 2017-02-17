<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class Columns extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $adminData = NULL;
        if ($this->getView()->editmode) {
            $adminData = \Toolbox\Tool\ElementBuilder::buildElementConfig('columns', $this->getView());
        }

        $equalHeight = $this->getView()->checkbox('equalHeight')->isChecked() && !$this->getView()->editmode;
        $type = $this->getView()->select('type')->getData();

        $partialName = '';
        $columns = [];

        if (!empty($type)) {

            $t = explode('_', $type);
            if ($this->getView()->toolboxHelper()->templateExists($this->getView(), 'toolbox/columns/' . $type . '.php')) {
                $partialName = $type;
            } else {
                $partialName = $t[0];
            }

            //remove "column" in string.
            $_columns = array_splice($t, 1);

            $offset = NULL;

            foreach ($_columns as $i => $column) {

                $columnClass = $column;
                if (substr($column, 0, 1) === 'o') {
                    $offset = (int)substr($column, -1);
                    //skip column, because of offset column.
                    continue;
                }

                $columns[] = [
                    'offset'      => $offset,
                    'btClass'     => $columnClass,
                    'columnClass' => 'toolbox-column' . ($equalHeight ? ' equal-height-item' : ''),
                    'name'        => 'column_' . $i
                ];

                $offset = NULL;
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

    public function getBrickHtmlTagOpen($brick)
    {
        return '';
    }

    public function getBrickHtmlTagClose($brick)
    {
        return '';
    }
}