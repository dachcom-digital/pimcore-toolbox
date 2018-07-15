<?php

use Pimcore\Kernel;

class TestAppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundlesToCollection(\Pimcore\HttpKernel\BundleCollection\BundleCollection $collection)
    {
        $collection->addBundle(new \ToolboxBundle\ToolboxBundle());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
        \Pimcore::setKernel($this);
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(function (\Symfony\Component\DependencyInjection\ContainerBuilder $container) use ($loader) {
            $container->addCompilerPass(new \Toolbox\Test\DependencyInjection\MonologChannelLoggerPass(), \Symfony\Component\DependencyInjection\Compiler\PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        });
    }
}
