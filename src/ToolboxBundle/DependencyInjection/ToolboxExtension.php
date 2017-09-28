<?php

namespace ToolboxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use ToolboxBundle\Service\ConfigManager;

class ToolboxExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configManagerDefinition = $container->getDefinition(ConfigManager::class);
        $configManagerDefinition->addMethodCall('setConfig', [ $config ]);

        $calculators = $config['theme']['calculators'];
        foreach($calculators as $confName => $confValue) {
            $definition = $container->findDefinition($confName);
            $definition->setClass($confValue);
        }
    }
}