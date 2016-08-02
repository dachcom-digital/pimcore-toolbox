<?php

namespace Toolbox;

use Pimcore\API\Plugin as PluginLib;

use Toolbox\Plugin\Install;

class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface {

    public function __construct($jsPaths = null, $cssPaths = null, $alternateIndexDir = null)
    {
        parent::__construct($jsPaths, $cssPaths);

        define('TOOLBOX_CONFIGURATION_FILE', PIMCORE_CONFIGURATION_DIRECTORY . '/toolbox_configuration.php');
        define('TOOLBOX_CORE_CONFIGURATION_FILE', PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/toolbox_configuration.php');
    }

    public function preDispatch($e)
    {
        $e->getTarget()->registerPlugin(new Controller\Plugin\HtmlParser());
        $e->getTarget()->registerPlugin(new Controller\Plugin\Frontend());

        $front  = \Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $staticAssetRoute = new \Zend_Controller_Router_Route_Regex(
            'website\/static\/(js|css)\/(.*?)\.(js|css)',
            array(
                'module' => 'Toolbox',
                'controller' => 'minify',
                'action' => 'render',
            ),
            array(
                1 => 'assetType',
                2 => 'fileName',
                3 => 'fileExtension'
            )
        );

        $staticSettingsRoute = new \Zend_Controller_Router_Route(
            '/toolbox-ckeditor-style.js',
            array(
                'module' => 'Toolbox',
                'controller' => 'Admin_Settings',
                'action' => 'ckeditor-style',
            )
        );

        $router->addRoute('toolbox_static_assets', $staticAssetRoute);
        $router->addRoute('toolbox_static_adminsettings', $staticSettingsRoute);
    }

    public function init()
    {
        parent::init();
    }

	public static function install()
    {
        $install = new Install();
        $install->installConfigFile();
        $install->installAdminTranslations();
        $install->addUserData();

        return 'Toolbox has been successfully installed.';
	}

	public static function uninstall()
    {
        return true;
	}

	public static function isInstalled()
    {
        $install = new Install();
        return $install->isInstalled();
	}

    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH . '/Toolbox/lang';
    }

    /**
     *
     * @param string $language
     * @return string $languageFile for the specified language relative to plugin directory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory() . "/$language.csv")) {
            return '/Toolbox/lang/$language.csv';
        }

        return '/Toolbox/lang/en.csv';
    }
}
