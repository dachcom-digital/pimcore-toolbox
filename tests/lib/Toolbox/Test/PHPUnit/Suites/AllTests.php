<?php

namespace Toolbox\Test\PHPUnit\Suites;

use Toolbox\Test\Setup;
use Toolbox\Test\SuiteBase;
use PHPUnit\Framework\TestSuite;

class AllTests extends SuiteBase
{
    public static function suite()
    {
        self::bootKernel();

        Setup::setupPimcore();
        Setup::setupBundle();

        $suite = new TestSuite('Models');

        $tests = [
            '\\Toolbox\\Test\\PHPUnit\\Suites\\Zone',
        ];

        shuffle($tests);
        echo "Created the following execution order:\n";

        foreach ($tests as $test) {
            echo '    - '.$test."\n";

            $suite->addTestSuite($test);
        }

        echo "Install Test Data:\n";
        \Toolbox\Test\Data::createData();

        return $suite;
    }
}
