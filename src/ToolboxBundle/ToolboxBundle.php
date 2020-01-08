<?php

namespace ToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ToolboxBundle\DependencyInjection\Compiler\CalculatorRegistryPass;
use ToolboxBundle\DependencyInjection\Compiler\MembersBundlePass;
use ToolboxBundle\Tool\Install;

class ToolboxBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'dachcom-digital/toolbox';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MembersBundlePass());
        $container->addCompilerPass(new CalculatorRegistryPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Install::class);
    }

    /**
     * @return string[]
     */
    public function getJsPaths()
    {
        return [
            '/admin/toolbox-ckeditor-object-style.js',
            '/bundles/toolbox/js/toolbox-ckeditor-plugins.js',
            '/bundles/toolbox/js/document/edit.js',
            '/bundles/toolbox/js/startup.js',
        ];
    }

    /**
     * @return string[]
     */
    public function getEditmodeJsPaths()
    {
        return [
            '/bundles/toolbox/js/backend/toolbox.js',
            '/bundles/toolbox/js/toolbox-ckeditor-plugins.js',
            '/bundles/toolbox/js/document/tags/areablock.js',
            '/bundles/toolbox/js/document/tags/dynamiclink.js',
            '/bundles/toolbox/js/document/tags/googlemap.js',
            '/bundles/toolbox/js/document/tags/parallaximage.js',
            '/bundles/toolbox/js/document/tags/columnadjuster.js',
            '/bundles/toolbox/js/document/tags/vhs.js',
            '/bundles/toolbox/js/document/tags/vhs/editor.js',
        ];
    }

    /**
     * @return string[]
     */
    public function getEditmodeCssPaths()
    {
        $cssFiles = [
            '/bundles/toolbox/css/admin.css',
            '/bundles/toolbox/css/admin_uikit.css'
        ];

        $pimcoreVersion = preg_replace('/[^0-9.]/', '', \Pimcore\Version::getVersion());
        if (version_compare($pimcoreVersion, '5.3.0', '>=')) {
            $cssFiles[] = '/bundles/toolbox/css/admin_53.css';
        }

        if (version_compare($pimcoreVersion, '5.4.4', '>=')) {
            $cssFiles[] = '/bundles/toolbox/css/admin_544.css';
        }

        return $cssFiles;
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
