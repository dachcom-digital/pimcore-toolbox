<?php

namespace DachcomBundle\Test\App\Pimcore;

use Pimcore\Config;

class TestConfig extends Config
{

    /**
     * @param string $offset
     *
     * @return string
     */
    public function offsetGet($offset)
    {
        parent::$systemConfig = null;

        return parent::offsetGet($offset);
    }

}