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
            '\\Toolbox\\Test\\PHPUnit\\Suites\\AreaManager',
            '\\Toolbox\\Test\\PHPUnit\\Suites\\ColumnCalculator',
        ];

        shuffle($tests);
        echo 'Created the following execution order:' . PHP_EOL;

        foreach ($tests as $test) {
            echo '    - ' . $test . PHP_EOL;
            $suite->addTestSuite($test);
        }

        echo 'Install Test Data:' . PHP_EOL;
        \Toolbox\Test\Data::createData();
        echo PHP_EOL;

        return $suite;
    }
}
