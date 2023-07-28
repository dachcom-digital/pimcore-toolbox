<?php

namespace ToolboxBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ToolboxBundle\DependencyInjection\Compiler\AreaBrickAutoloadWatcherPass;
use ToolboxBundle\DependencyInjection\Compiler\AreaBrickRegistryPass;
use ToolboxBundle\DependencyInjection\Compiler\CalculatorRegistryPass;
use ToolboxBundle\DependencyInjection\Compiler\EditableHandlerPass;
use ToolboxBundle\DependencyInjection\Compiler\MembersBundlePass;
use ToolboxBundle\DependencyInjection\Compiler\StoreProviderPass;
use ToolboxBundle\Tool\Install;

class ToolboxBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public const PACKAGE_NAME = 'dachcom-digital/toolbox';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AreaBrickRegistryPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 255);
        $container->addCompilerPass(new AreaBrickAutoloadWatcherPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -255);
        $container->addCompilerPass(new EditableHandlerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -255);
        $container->addCompilerPass(new MembersBundlePass());
        $container->addCompilerPass(new CalculatorRegistryPass());
        $container->addCompilerPass(new StoreProviderPass());
    }

    public function getInstaller(): Install
    {
        return $this->container->get(Install::class);
    }

    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
