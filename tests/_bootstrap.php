<?php

if (!defined('PIMCORE_PROJECT_ROOT')) {
    define(
        'PIMCORE_PROJECT_ROOT',
        getenv('PIMCORE_PROJECT_ROOT')
            ?: getenv('REDIRECT_PIMCORE_PROJECT_ROOT')
            ?: realpath(getcwd())
    );
}

if (!defined('TESTS_PATH')) {
    define('TESTS_PATH', __DIR__);
}

define('PIMCORE_CLASS_DIRECTORY', __DIR__ . '/tmp/var/classes');
define('PIMCORE_TEST', true);

require_once PIMCORE_PROJECT_ROOT . '/pimcore/config/bootstrap.php';

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader->add('Toolbox\Test', [__DIR__.'/lib']);
$loader->add('Pimcore\Model\DataObject', [__DIR__ . '/tmp/var/classes/DataObject']);
$loader->addPsr4('Pimcore\\Model\\DataObject\\', PIMCORE_CLASS_DIRECTORY.'/DataObject', true);

require __DIR__ . '/app/TestAppKernel.php';