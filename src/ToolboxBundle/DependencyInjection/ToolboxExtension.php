<?php

namespace ToolboxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class ToolboxExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configManagerDefinition = $container->getDefinition('toolbox.config_manager');
        $configManagerDefinition->addMethodCall('setConfig', [ $config ]);

        $toolboxLayout = strtolower($config['theme']['layout']);

        $columnCalculator = 'toolbox.calculator.' . $toolboxLayout . '.column';
        $container->setAlias('toolbox.calculator.column_calculator', new Alias($columnCalculator, false));

        $slideColumnCalculator = 'toolbox.calculator.' . $toolboxLayout . '.slide_column';
        $container->setAlias('toolbox.calculator.slide_column_calculator', new Alias($slideColumnCalculator, false));

    }

}