<?php

namespace ToolboxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use ToolboxBundle\Manager\ConfigManager;
use ToolboxBundle\Resolver\ContextResolver;

class ToolboxExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    protected $contextMergeData = [];

    /**
     * @var array
     */
    protected $contextConfigData = [];

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        // prevent throwing exception if no gm key has been defined.
        if ($container->hasParameter('pimcore_system_config.services.google.browserapikey') === false) {
            $container->setParameter('pimcore_system_config.services.google.browserapikey', null);
        }
        if ($container->hasParameter('pimcore_system_config.services.google.simpleapikey') === false) {
            $container->setParameter('pimcore_system_config.services.google.simpleapikey', null);
        }

        $selfConfigs = $container->getExtensionConfig($this->getAlias());

        $rootConfigs = [];
        foreach ($selfConfigs as $rootConfig) {
            unset($rootConfig['context'], $rootConfig['context_resolver']);
            $rootConfigs[] = $rootConfig;
        }

        $contextMerge = [];
        foreach ($selfConfigs as $config) {
            if (isset($config['context'])) {
                foreach ($config['context'] as $contextName => $contextConfig) {
                    if (isset($contextConfig['settings']['merge_with_root'])) {
                        $contextMerge[$contextName] = $contextConfig['settings']['merge_with_root'];
                    }
                }
            }
        }

        $data = [];

        //get context data
        foreach ($selfConfigs as $config) {
            if (isset($config['context'])) {
                foreach ($config['context'] as $contextName => $contextConfig) {
                    if (!isset($contextMerge[$contextName]) || $contextMerge[$contextName] !== true) {
                        continue;
                    }

                    $cleanContextConfig = $contextConfig;
                    unset($cleanContextConfig['settings']);
                    $this->contextConfigData[$contextName][] = $cleanContextConfig;
                }
            }
        }

        //get context merge data
        foreach ($contextMerge as $contextName => $merge) {
            if ($merge === false) {
                continue;
            }

            foreach ($rootConfigs as $rootConfig) {
                $data[] = [
                    'context' => [
                        $contextName => $rootConfig
                    ]
                ];
            }
        }

        $this->contextMergeData = $data;
    }

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //append merge data
        foreach ($this->contextMergeData as $append) {
            $configs[] = $append;
        }

        //append custom context data
        foreach ($this->contextConfigData as $contextName => $contextConfigs) {
            foreach ($contextConfigs as $el) {
                $configs[] = [
                    'context' => [
                        $contextName => $el
                    ]
                ];
            }
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->validateToolboxContextConfig($config);

        $contextResolver = $config['context_resolver'];
        unset($config['context_resolver']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configManagerDefinition = $container->getDefinition(ConfigManager::class);
        $configManagerDefinition->setPublic(true);

        $config = $this->handleCalculatorDeprecation($config, $container);

        $configManagerDefinition->addMethodCall('setConfig', [$config]);

        //context resolver
        $definition = $container->getDefinition(ContextResolver::class);
        $definition->setClass($contextResolver);
    }

    /**
     * @param array $config
     */
    private function validateToolboxContextConfig($config)
    {
        foreach ($config['context'] as $contextId => $contextConfig) {
            //check if theme is same since it's not possible to merge different themes
            if ($contextConfig['settings']['merge_with_root'] === true && isset($contextConfig['theme']) && $contextConfig['theme']['layout'] !== $config['theme']['layout']) {
                @trigger_error(sprintf(
                    'toolbox context conflict for "%s": merged context cannot have another theme than "%s", "%s" given.',
                    $contextId,
                    $config['theme']['layout'],
                    $contextConfig['theme']['layout']
                ), E_USER_ERROR);
            }
        }
    }

    /**
     * @deprecated since 2.3. gets removed in 4.0
     *
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return mixed
     */
    private function handleCalculatorDeprecation($config, ContainerBuilder $container)
    {
        $taggedCalculator = $container->findTaggedServiceIds('toolbox.calculator', true);

        $defaultColumnCalc = 'ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator';
        $defaultSlideColumnCalc = 'ToolboxBundle\Calculator\Bootstrap4\SlideColumnCalculator';

        $calculators = $config['theme']['calculators'];
        $missingTags = [];
        foreach ($calculators as $confName => $confValue) {
            if ($confName === 'ToolboxBundle\Calculator\ColumnCalculator') {
                if ($calculators['column_calculator'] !== $confValue && $confValue !== $defaultColumnCalc) {
                    $calculators['column_calculator'] = $confValue;
                }
                if (!in_array($confValue, array_keys($taggedCalculator))) {
                    $missingTags[] = [$confValue, 'column'];
                }
            } elseif ($confName === 'ToolboxBundle\Calculator\SlideColumnCalculator') {
                if ($calculators['slide_calculator'] !== $confValue && $confValue !== $defaultSlideColumnCalc) {
                    $calculators['slide_calculator'] = $confValue;
                }
                if (!in_array($confValue, array_keys($taggedCalculator))) {
                    $missingTags[] = [$confValue, 'slide_column'];
                }
            }
        }

        $config['theme']['calculators'] = $calculators;

        if (!empty($missingTags)) {
            $container->setParameter('toolbox.deprecation.calculator_tags', $missingTags);
        }

        return $config;
    }
}
