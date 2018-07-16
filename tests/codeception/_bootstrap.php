<?php

use Toolbox\Test\Util\Autoloader;

if (!defined('PIMCORE_PROJECT_ROOT')) {
    define(
        'PIMCORE_PROJECT_ROOT',
        getenv('PIMCORE_PROJECT_ROOT')
            ?: getenv('REDIRECT_PIMCORE_PROJECT_ROOT')
            ?: realpath(getcwd())
    );
}

require_once PIMCORE_PROJECT_ROOT . '/vendor/autoload.php';

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
Autoloader::addNamespace('Pimcore\Model\DataObject', __DIR__ . '/_output/var/classes/DataObject');
Autoloader::addNamespace('Toolbox\Test\Unit', __DIR__ . '/unit');
Autoloader::addNamespace('Pimcore\Tests', PIMCORE_PROJECT_ROOT . '/pimcore/tests/_support');

if (!defined('TESTS_PATH')) {
    define('TESTS_PATH', __DIR__);
}

define('PIMCORE_CLASS_DIRECTORY', __DIR__ . '/tmp/var/classes');
define('PIMCORE_TEST', true);