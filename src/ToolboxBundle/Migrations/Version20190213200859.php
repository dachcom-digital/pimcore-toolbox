<?php

namespace ToolboxBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Filesystem\Filesystem;
use ToolboxBundle\Tool\Install;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190213200859 extends AbstractPimcoreMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists(Install::SYSTEM_CONFIG_DIR_PATH . '/config.yml')) {
            $fileSystem->remove(Install::SYSTEM_CONFIG_DIR_PATH . '/config.yml');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // nothing to do.
    }
}
