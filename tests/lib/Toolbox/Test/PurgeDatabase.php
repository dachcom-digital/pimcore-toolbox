<?php

namespace Toolbox\Test;

use Pimcore\Model\DataObject\Concrete;

final class PurgeDatabase
{
    public function purge()
    {
        $list = Concrete::getList();
        $list->setCondition('o_id <> 1');
        $list->load();

        foreach ($list->getObjects() as $obj) {
            $obj->delete();
        }
    }
}