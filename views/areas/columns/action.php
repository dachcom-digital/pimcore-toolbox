<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class Columns extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $equalHeight = $this->view->checkbox('equalHeight')->isChecked() && !$this->view->editmode;
        $type = $this->view->select('type')->getData();

        $partialName = '';
        $columns = [];

        if (!empty($type)) {

            $t = explode('_', $type);
            if ($this->view->toolboxHelper()->templateExists($this->view, 'toolbox/columns/' . $type . '.php')) {
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

        $this->view->assign([
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