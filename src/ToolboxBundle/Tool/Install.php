<?php

namespace ToolboxBundle\Tool;

use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\Document\DocType;
use Pimcore\Model\Translation;

class Install extends SettingsStoreAwareInstaller
{
    public const SYSTEM_CONFIG_DIR_PATH = PIMCORE_PRIVATE_VAR . '/bundles/ToolboxBundle';

    public function install(): void
    {
        $this->installTranslations();
        $this->installDocumentTypes();

        parent::install();
    }

    private function installTranslations(): void
    {
        $csvAdmin = __DIR__ . '/../Resources/install//admin-translations/data.csv';

        try {
            Translation::importTranslationsFromFile($csvAdmin, Translation::DOMAIN_ADMIN, true, \Pimcore\Tool\Admin::getLanguages());
        } catch (\Exception $e) {
            throw new InstallationException(sprintf('Failed to install admin translations. error was: "%s"', $e->getMessage()));
        }
    }

    private function installDocumentTypes(): void
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

        $type->setValues([
            'name'       => $elementName,
            'controller' => 'ToolboxBundle\Controller\SnippetController::teaserAction',
            'type'       => 'snippet',
            'priority'   => 0
        ]);

        try {
            $type->getDao()->save();
        } catch (\Exception $e) {
            throw new InstallationException(sprintf('Failed to save document type "%s". Error was: "%s"', $elementName, $e->getMessage()));
        }
    }
}
