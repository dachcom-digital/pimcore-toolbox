<?php

namespace ToolboxBundle\Tool;

use PackageVersions\Versions;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\Document\DocType;
use Pimcore\Model\Translation\Admin;
use Symfony\Component\Filesystem\Filesystem;
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
     * @var string
     */
    private $currentVersion;

    /**
     * Install constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->installSourcesPath = __DIR__ . '/../Resources/install';
        $this->fileSystem = new Filesystem();

        $this->currentVersion = Versions::getVersion(ToolboxBundle::PACKAGE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->installOrUpdateConfigFile();
        $this->importTranslations();
        $this->installDocumentTypes();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        $this->installOrUpdateConfigFile();
        $this->importTranslations();
        $this->installDocumentTypes();
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
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUpdated()
    {
        $needUpdate = false;
        if ($this->fileSystem->exists(self::SYSTEM_CONFIG_FILE_PATH)) {
            $config = Yaml::parse(file_get_contents(self::SYSTEM_CONFIG_FILE_PATH));
            if ($config['version'] !== $this->currentVersion) {
                $needUpdate = true;
            }
        }

        return $needUpdate;
    }

    /**
     * imports admin-translations
     *
     * @throws \Exception
     */
    private function importTranslations()
    {
        Admin::importTranslationsFromFile($this->installSourcesPath . '/admin-translations/data.csv', true);
    }

    /**
     * @return bool
     */
    private function installDocumentTypes()
    {
        // get list of types
        $list = new DocType\Listing();
        $list->load();

        $skipInstall = false;
        $elementName = 'Teaser Snippet';

        foreach ($list->getDocTypes() as $type) {
            if ($type->getName() === $elementName) {
                $skipInstall = true;
                break;
            }
        }

        if ($skipInstall) {
            return false;
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
        $type->save();
    }

    /**
     * copy sample config file - if not exists.
     */
    private function installOrUpdateConfigFile()
    {
        if (!$this->fileSystem->exists(self::SYSTEM_CONFIG_DIR_PATH)) {
            $this->fileSystem->mkdir(self::SYSTEM_CONFIG_DIR_PATH);
        }

        $config = ['version' => $this->currentVersion];
        $yml = Yaml::dump($config);
        file_put_contents(self::SYSTEM_CONFIG_FILE_PATH, $yml);
    }
}