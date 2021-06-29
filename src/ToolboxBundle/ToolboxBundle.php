<?php

namespace ToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ToolboxBundle\DependencyInjection\Compiler\CalculatorRegistryPass;
use ToolboxBundle\DependencyInjection\Compiler\MembersBundlePass;
use ToolboxBundle\DependencyInjection\Compiler\StoreProviderPass;

class ToolboxBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'dachcom-digital/toolbox';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MembersBundlePass());
        $container->addCompilerPass(new CalculatorRegistryPass());
        $container->addCompilerPass(new StoreProviderPass());
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
        $legacyVersion = version_compare(self::getPimcoreVersion(), '6.8.0', '<');

        $routeJsPaths = [
            sprintf('/bundles/toolbox/js/document/%s/_route.js', $legacyVersion ? 'tags' : 'editables')
        ];

        $alwaysPaths = [
            '/bundles/toolbox/js/backend/toolbox.js',
            '/bundles/toolbox/js/toolbox-ckeditor-plugins.js',
            '/bundles/toolbox/js/document/editables/dynamiclink.js',
            '/bundles/toolbox/js/document/editables/googlemap.js',
            '/bundles/toolbox/js/document/editables/parallaximage.js',
            '/bundles/toolbox/js/document/editables/columnadjuster.js',
            '/bundles/toolbox/js/document/editables/vhs.js',
            '/bundles/toolbox/js/document/editables/vhs/editor.js',
        ];

        $dynamicPaths = $legacyVersion ? [
            '/bundles/toolbox/js/document/tags/areablock.js',
            '/bundles/toolbox/js/document/tags/dynamiclink.js',
            '/bundles/toolbox/js/document/tags/googlemap.js',
            '/bundles/toolbox/js/document/tags/parallaximage.js',
            '/bundles/toolbox/js/document/tags/columnadjuster.js',
            '/bundles/toolbox/js/document/tags/vhs.js',
        ] : [
            '/bundles/toolbox/js/document/editables/areablock.js'
        ];

        return array_merge($routeJsPaths, $alwaysPaths, $dynamicPaths);
    }

    public function getEditmodeCssPaths(): array
    {
        $cssFiles = [
            '/bundles/toolbox/css/admin.css',
            '/bundles/toolbox/css/admin_uikit.css',
            '/bundles/toolbox/css/admin_544.css'
        ];
//
//        if (version_compare(self::getPimcoreVersion(), '5.3.0', '>=')) {
//            $cssFiles[] = '/bundles/toolbox/css/admin_53.css';
//        }
//
//        if (version_compare(self::getPimcoreVersion(), '5.4.4', '>=')) {
//            $cssFiles[] = '';
//        }

        return $cssFiles;
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    protected static function getPimcoreVersion(): string
    {
        return preg_replace('/[^0-9.]/', '', \Pimcore\Version::getVersion());
    }
}
