<?php

namespace Toolbox\Test;

class Data
{
    /**
     * @param $serviceId
     *
     * @return object
     */
    private static function get($serviceId)
    {
        return \Pimcore::getKernel()->getContainer()->get($serviceId);
    }

    /**
     * Create Test Data.
     */
    public static function createData()
    {
        $purger = new PurgeDatabase();
        $purger->purge();

        //self::setup();
    }

    public static function setup()
    {
    }
}
