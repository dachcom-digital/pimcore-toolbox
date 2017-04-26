<?php

namespace Toolbox;

use Pimcore\API\Plugin as PluginLib;

use Toolbox\Plugin\Install;

class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface
{
    /**
     * Plugin constructor.
     *
     * @param null $jsPaths
     * @param null $cssPaths
     * @param null $alternateIndexDir
     */
    public function __construct($jsPaths = NULL, $cssPaths = NULL, $alternateIndexDir = NULL)
    {
        parent::__construct($jsPaths, $cssPaths);

        define('TOOLBOX_CONFIGURATION_FILE', PIMCORE_CONFIGURATION_DIRECTORY . '/toolbox_configuration.php');
        define('TOOLBOX_CORE_CONFIGURATION_FILE', PIMCORE_PLUGINS_PATH . '/Toolbox/var/config/toolbox_configuration.php');
    }

    /**
     * @param $e
     */
    public function preDispatch($e)
    {
        //check if assetHandler is enabled.
        $useAssetHandler = Config::getConfig()->enableAssetHandler === TRUE;

        if($useAssetHandler) {
            $e->getTarget()->registerPlugin(new Controller\Plugin\HtmlParser());
        }

        $e->getTarget()->registerPlugin(new Controller\Plugin\Frontend($useAssetHandler));

        $front = \Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $staticAssetRoute = new \Zend_Controller_Router_Route_Regex(
            'website\/static\/(js|css)\/(.*?)\.(js|css)',
            [
                'module'     => 'Toolbox',
                'controller' => 'minify',
                'action'     => 'render',
            ],
            [
                1 => 'assetType',
                2 => 'fileName',
                3 => 'fileExtension'
            ]
        );

        $ckeditorAreaStyle = new \Zend_Controller_Router_Route(
            '/toolbox-ckeditor-style.js',
            [
                'module'     => 'Toolbox',
                'controller' => 'Admin_Settings',
                'action'     => 'ck-editor-area-style',
            ]
        );

        $ckeditorObjectStyle = new \Zend_Controller_Router_Route(
            '/toolbox-ckeditor-object-style.js',
            [
                'module'     => 'Toolbox',
                'controller' => 'Admin_Settings',
                'action'     => 'ck-editor-object-style',
            ]
        );

        $router->addRoute('toolbox_static_assets', $staticAssetRoute);
        $router->addRoute('toolbox_ckeditor_area', $ckeditorAreaStyle);
        $router->addRoute('toolbox_ckeditor_object', $ckeditorObjectStyle);
    }

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public static function install()
    {
        $install = new Install();
        $install->installConfigFile();
        $install->installAdminTranslations();
        $install->addUserData();

        return 'Toolbox has been successfully installed.';
    }

    /**
     * @return bool
     */
    public static function uninstall()
    {
        return TRUE;
    }

    /**
     * @return bool
     */
    public static function isInstalled()
    {
        $install = new Install();

        return $install->isInstalled();
    }

    /**
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH . '/Toolbox/lang';
    }

    /**
     *  *
     *  * @param string $language
     *  * @return string $languageFile for the specified language relative to plugin directory
     *  */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory() . "/$language.csv")) {
            return "/Toolbox/lang/$language.csv";
        }

        return '/Toolbox/lang/en.csv';
    }
}
