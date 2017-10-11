<?php

namespace ToolboxBundle\Tool;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;

use Symfony\Component\Filesystem\Filesystem;
use Pimcore\Model\Translation\Admin;
use Symfony\Component\Yaml\Yaml;
use ToolboxBundle\ToolboxBundle;

class Install extends AbstractInstaller
{
    const SYSTEM_CONFIG_DIR_PATH = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle';

    const SYSTEM_CONFIG_FILE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config.yml';

    /**
     * @var string
     */
    private $installSourcesPath;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * Install constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->installSourcesPath = __DIR__ . '/../Resources/install';
        $this->fileSystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->installOrUpdateConfigFile();
        $this->importTranslations();

        return TRUE;
    }

    /**
     * For now, just update the config file to the current version.
     * {@inheritdoc}
     */
    public function update()
    {
        $this->installOrUpdateConfigFile();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        $target = self::SYSTEM_CONFIG_FILE_PATH;
        if ($this->fileSystem->exists($target)) {
            $this->fileSystem->rename(
                $target,
                PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config_backup.yml'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled()
    {
        return $this->fileSystem->exists(self::SYSTEM_CONFIG_FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeInstalled()
    {
        return !$this->fileSystem->exists(self::SYSTEM_CONFIG_FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUninstalled()
    {
        return $this->fileSystem->exists(self::SYSTEM_CONFIG_FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUpdated()
    {
        $needUpdate = FALSE;
        if ($this->fileSystem->exists(self::SYSTEM_CONFIG_FILE_PATH)) {
            $config = Yaml::parse(file_get_contents(self::SYSTEM_CONFIG_FILE_PATH));
            if($config['version'] !== ToolboxBundle::BUNDLE_VERSION) {
                $needUpdate = TRUE;
            }
        }

        return $needUpdate;
    }

    /**
     * imports admin-translations
     * @throws \Exception
     */
    private function importTranslations()
    {
        Admin::importTranslationsFromFile($this->installSourcesPath . '/admin-translations/data.csv', TRUE);
    }

    /**
     * copy sample config file - if not exists.
     */
    private function installOrUpdateConfigFile()
    {
        if(!$this->fileSystem->exists(self::SYSTEM_CONFIG_DIR_PATH)) {
            $this->fileSystem->mkdir(self::SYSTEM_CONFIG_DIR_PATH);
        }

        $config = ['version' => ToolboxBundle::BUNDLE_VERSION];
        $yml = Yaml::dump($config);
        file_put_contents(self::SYSTEM_CONFIG_FILE_PATH, $yml);
    }
}