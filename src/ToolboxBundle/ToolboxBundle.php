<?php

namespace ToolboxBundle;

use ToolboxBundle\Tool\Install;
use ToolboxBundle\DependencyInjection\Compiler\CalculatorRegistryPass;
use ToolboxBundle\DependencyInjection\Compiler\MembersBundlePass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ToolboxBundle extends AbstractPimcoreBundle
{
    const BUNDLE_VERSION = '2.4.4';

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MembersBundlePass());
        $container->addCompilerPass(new CalculatorRegistryPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return self::BUNDLE_VERSION;
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
            '/bundles/toolbox/js/document/helpers.js',
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
            '/bundles/toolbox/js/document/tags/vhs.js',
            '/bundles/toolbox/js/document/tags/columnadjuster.js'
        ];
    }

    /**
     * @return string[]
     */
    public function getEditmodeCssPaths()
    {
        return [
            '/bundles/toolbox/css/admin.css',
        ];
    }

}
