<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
use ToolboxBundle\DependencyInjection\Compiler\NormalizerRegistryPass;
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
        $container->addCompilerPass(new NormalizerRegistryPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
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
