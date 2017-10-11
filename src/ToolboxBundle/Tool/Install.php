<?php

namespace ToolboxBundle\Tool;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;

use Symfony\Component\Filesystem\Filesystem;
use Pimcore\Model\Translation\Admin;
use Symfony\Component\Yaml\Yaml;
use ToolboxBundle\ToolboxBundle;

class Install extends AbstractInstaller
{
    /**
     * @var string
     */
    private $installSourcesPath;

    /**
     * @var Filesystem
     */
    private $fileSystem;

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
        $this->copyConfigFile();
        $this->importTranslations();

        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        $target = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config.yml';

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
        $target = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config.yml';

        return $this->fileSystem->exists($target);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeInstalled()
    {
        $target = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config.yml';

        return !$this->fileSystem->exists($target);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUninstalled()
    {
        $target = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config.yml';

        return $this->fileSystem->exists($target);
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
        return FALSE;
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
    private function copyConfigFile()
    {
        $target = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle/config.yml';
        if (!$this->fileSystem->exists($target)) {
            $config = ['version' => ToolboxBundle::BUNDLE_VERSION];
            $yml = Yaml::dump($config);
            file_put_contents($target, $yml);
        }
    }
}