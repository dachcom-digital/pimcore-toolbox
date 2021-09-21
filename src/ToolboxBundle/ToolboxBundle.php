<?php

namespace ToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ToolboxBundle\DependencyInjection\Compiler\CalculatorRegistryPass;
use ToolboxBundle\DependencyInjection\Compiler\MembersBundlePass;
use ToolboxBundle\DependencyInjection\Compiler\StoreProviderPass;
use ToolboxBundle\Tool\Install;

class ToolboxBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public const PACKAGE_NAME = 'dachcom-digital/toolbox';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MembersBundlePass());
        $container->addCompilerPass(new CalculatorRegistryPass());
        $container->addCompilerPass(new StoreProviderPass());
    }

    public function getInstaller(): Install
    {
        return $this->container->get(Install::class);
    }

    public function getJsPaths(): array
    {
        return [
            '/admin/toolbox-ckeditor-object-style.js',
            '/bundles/toolbox/js/toolbox-ckeditor-plugins.js',
            '/bundles/toolbox/js/document/edit.js',
            '/bundles/toolbox/js/startup.js',
        ];
    }

    public function getEditmodeJsPaths(): array
    {
        return [
            '/bundles/toolbox/js/backend/toolbox.js',
            '/bundles/toolbox/js/toolbox-ckeditor-plugins.js',
            '/bundles/toolbox/js/document/editables/areablock.js',
            '/bundles/toolbox/js/document/editables/dynamiclink.js',
            '/bundles/toolbox/js/document/editables/googlemap.js',
            '/bundles/toolbox/js/document/editables/parallaximage.js',
            '/bundles/toolbox/js/document/editables/columnadjuster.js',
            '/bundles/toolbox/js/document/editables/vhs.js',
            '/bundles/toolbox/js/document/editables/vhs/editor.js',
        ];
    }

    public function getEditmodeCssPaths(): array
    {
        return [
            '/bundles/toolbox/css/admin.css',
            '/bundles/toolbox/css/admin_uikit.css'
        ];
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    /**
     * @return string
     */
    protected static function getPimcoreVersion()
    {
        return preg_replace('/[^0-9.]/', '', \Pimcore\Version::getVersion());
    }
}
