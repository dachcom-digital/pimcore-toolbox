<?php

use Pimcore\Config;
use Symfony\Component\Debug\Debug;

require_once PIMCORE_PROJECT_ROOT . '/pimcore/config/constants.php';
require_once PIMCORE_PROJECT_ROOT . '/pimcore/lib/helper-functions.php';

$environment = Config::getEnvironment();
$debug = Config::getEnvironmentConfig()->activatesKernelDebugMode($environment);

if ($debug) {
    Debug::enable();
    @ini_set('display_errors', 'On');
}

$kernel = new \Toolbox\Test\App\TestAppKernel($environment, $debug);
return $kernel;