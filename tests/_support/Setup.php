<?php

namespace Toolbox\Test;

use Doctrine\DBAL\DriverManager;
use ToolboxBundle\Tool\Install;

class Setup
{
    private static $pimcoreSetupDone = false;
    private static $dachcomBundleSetupDone = false;

    public static function setupPimcore()
    {
        if (getenv('DACHCOM_BUNDLE_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$pimcoreSetupDone) {
            return;
        }

        $connection = \Pimcore::getContainer()->get('database_connection');

        $dbName = $connection->getParams()['dbname'];
        $params = $connection->getParams();
        $config = $connection->getConfiguration();

        unset($params['url']);
        unset($params['dbname']);

        // use a dedicated setup connection as the framework connection is bound to the DB and will
        // fail if the DB doesn't exist
        $setupConnection = DriverManager::getConnection($params, $config);
        $schemaManager = $setupConnection->getSchemaManager();

        $databases = $schemaManager->listDatabases();
        if (in_array($dbName, $databases)) {
            $schemaManager->dropDatabase($connection->quoteIdentifier($dbName));
        }

        $schemaManager->createDatabase($connection->quoteIdentifier($dbName));

        if (!$connection->isConnected()) {
            $connection->connect();
        }

        $setup = new \Pimcore\Model\Tool\Setup();
        $setup->database();

        $setup->contents([
            'username' => 'admin',
            'password' => microtime()
        ]);

        static::$pimcoreSetupDone = true;
    }

    public static function setupBundle()
    {
        if (getenv('DACHCOM_BUNDLE_SKIP_DB_SETUP')) {
            return;
        }

        if (static::$dachcomBundleSetupDone) {
            return;
        }

        $installer = \Pimcore::getContainer()->get(Install::class);
        $installer->install();

        static::$dachcomBundleSetupDone = true;
    }
}