<?php

namespace ToolboxBundle\Tool;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\MigrationException;
use Doctrine\DBAL\Migrations\Version;
use Pimcore\Model\Document\DocType;
use Pimcore\Model\Translation\Admin;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Pimcore\Migrations\Migration\InstallMigration;
use Symfony\Component\Filesystem\Filesystem;

class Install extends MigrationInstaller
{
    const SYSTEM_CONFIG_DIR_PATH = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle';

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion(): string
    {
        return '00000001';
    }

    /**
     * @throws AbortMigrationException
     * @throws MigrationException
     */
    protected function beforeInstallMigration()
    {
        $markVersionsAsMigrated = true;

        // legacy:
        //   we switched from config to migration
        //   if config.yml exists, this instance needs to migrate
        //   so every migration needs to run.
        // fresh:
        //   skip all versions since they are not required anymore
        //   (fresh installation does not require any version migrations)
        $fileSystem = new Filesystem();
        if ($fileSystem->exists(self::SYSTEM_CONFIG_DIR_PATH . '/config.yml')) {
            $markVersionsAsMigrated = false;
        }

        if ($markVersionsAsMigrated === true) {
            $migrationConfiguration = $this->migrationManager->getBundleConfiguration($this->bundle);
            $this->migrationManager->markVersionAsMigrated($migrationConfiguration->getVersion($migrationConfiguration->getLatestVersion()));
        }

        $this->initializeFreshSetup();
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
        /** @var InstallMigration $migration */
        $migration = $version->getMigration();
        if ($migration->isDryRun()) {
            $this->outputWriter->write('<fg=cyan>DRY-RUN:</> Skipping installation');

            return;
        }
    }

    /**
     * @param Schema  $schema
     * @param Version $version
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
        /** @var InstallMigration $migration */
        $migration = $version->getMigration();
        if ($migration->isDryRun()) {
            $this->outputWriter->write('<fg=cyan>DRY-RUN:</> Skipping uninstallation');

            return;
        }

        // currently nothing to do.
    }

    /**
     * @param string|null $version
     *
     * @throws AbortMigrationException
     */
    protected function beforeUpdateMigration(string $version = null)
    {
        $this->installTranslations();
        $this->installDocumentTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * @throws AbortMigrationException
     */
    public function initializeFreshSetup()
    {
        $this->installTranslations();
        $this->installDocumentTypes();
    }

    /**
     * Imports admin-translations.
     *
     * @throws AbortMigrationException
     */
    private function installTranslations()
    {
        try {
            Admin::importTranslationsFromFile($this->getInstallSourcesPath() . '/admin-translations/data.csv', true);
        } catch (\Exception $e) {
            throw new AbortMigrationException(sprintf('Failed to install admin translations. error was: "%s"', $e->getMessage()));
        }
    }

    /**
     * @throws AbortMigrationException
     */
    private function installDocumentTypes()
    {
        // get list of types
        $list = new DocType\Listing();
        $list->getDao()->load();

        $skipInstall = false;
        $elementName = 'Teaser Snippet';

        /** @var DocType $type */
        foreach ($list->getDocTypes() as $type) {
            if ($type->getName() === $elementName) {
                $skipInstall = true;

                break;
            }
        }

        if ($skipInstall) {
            return;
        }

        $type = DocType::create();

        $data = [
            'name'       => $elementName,
            'module'     => 'ToolboxBundle',
            'controller' => 'Snippet',
            'action'     => 'teaser',
            'template'   => '',
            'type'       => 'snippet',
            'priority'   => 0
        ];

        $type->setValues($data);

        try {
            $type->getDao()->save();
        } catch (\Exception $e) {
            throw new AbortMigrationException(sprintf('Failed to save document type "%s". Error was: "%s"', $elementName, $e->getMessage()));
        }
    }

    /**
     * @return string
     */
    protected function getInstallSourcesPath()
    {
        return __DIR__ . '/../Resources/install';
    }
}
