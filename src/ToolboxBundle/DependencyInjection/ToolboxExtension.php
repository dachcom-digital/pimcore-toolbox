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
    public function prepend(ContainerBuilder $container): void
    {
        $hasTheme = false;

        $wysiwygEditor = null;
        if ($container->hasExtension('pimcore_tinymce') === true) {
            $wysiwygEditor = 'tiny_mce';
        }

        $coreLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $coreLoader->load('config.yaml');

        $loaded = [];

        foreach ($container->getExtensionConfig($this->getAlias()) as $toolboxConfigNode) {

            if (!empty($toolboxConfigNode['theme']['layout'])) {
                $hasTheme = true;
            }

            if (($toolboxConfigNode['enabled_core_areas'] ?? null) === null) {
                continue;
            }

            foreach ($toolboxConfigNode['enabled_core_areas'] as $areaName) {

                if (in_array($areaName, $loaded, true)) {
                    continue;
                }

                $coreLoader->load(sprintf('core_areas/%s.yaml', $areaName));
                $loaded[] = $areaName;
            }
        }

        // add default theme (b4) if not set
        if ($hasTheme === false) {
            $coreLoader->load('theme/bootstrap4_theme.yaml');
        }

        $container->setParameter('toolbox.wysiwyg_editor', $wysiwygEditor);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $contextAwareConfigs = $this->parseContextConfigs($configs);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $contextAwareConfigs);

        $this->validateToolboxContextConfig($config);
        $this->allocateGoogleMapsApiKey($container);

        $contextResolver = $config['context_resolver'];
        unset($config['context_resolver']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configManagerDefinition = $container->getDefinition(ConfigManager::class);
        $configManagerDefinition->addMethodCall('setConfig', [$config]);

        $container->setParameter('toolbox.area_brick.dialog_aware_bricks', $this->determinateConfigDialogAwareBricks($config));

        //context resolver
        $definition = $container->getDefinition(ContextResolver::class);
        $definition->setClass($contextResolver);
    }

    private function determinateConfigDialogAwareBricks(array $config): array
    {
        $configDialogAwareBricks = [];

        foreach ($config['areas'] as $areaId => $areaSection) {
            if (isset($areaSection['config_elements']) && count($areaSection['config_elements']) > 0) {
                $configDialogAwareBricks[] = $areaId;
            }
        }

        foreach ($config['context'] as $context) {
            foreach ($context['areas'] as $areaId => $areaSection) {
                if (isset($areaSection['config_elements']) && count($areaSection['config_elements']) > 0) {
                    $configDialogAwareBricks[] = $areaId;
                }
            }
        }

        return array_unique($configDialogAwareBricks);
    }

    private function allocateGoogleMapsApiKey(ContainerBuilder $container): void
    {
        $googleBrowserApiKey = null;
        $googleSimpleApiKey = null;

        $pimcoreGoogleBrowserApiKey = null;
        $pimcoreGoogleSimpleApiKey = null;

        /** @phpstan-ignore-next-line */
        if ($container->hasParameter('pimcore_google_marketing')) {
            $pimcoreGoogleMarketingSettings = $container->getParameter('pimcore_google_marketing');
            /** @phpstan-ignore-next-line */
            $pimcoreGoogleBrowserApiKey = $pimcoreGoogleMarketingSettings['browser_api_key'] ?? null;
            /** @phpstan-ignore-next-line */
            $pimcoreGoogleSimpleApiKey = $pimcoreGoogleMarketingSettings['simple_api_key'] ?? null;
        }

        // browser api key
        /** @phpstan-ignore-next-line */
        if ($container->hasParameter('toolbox_google_service_browser_api_key')) {
            $googleBrowserApiKey = $container->getParameter('toolbox_google_service_browser_api_key');
            /** @phpstan-ignore-next-line */
        } elseif ($pimcoreGoogleBrowserApiKey !== null) {
            $googleBrowserApiKey = $pimcoreGoogleBrowserApiKey;
        }

        //simple api key
        /** @phpstan-ignore-next-line */
        if ($container->hasParameter('pimcore_system_config.services.google.simpleapikey')) {
            $googleSimpleApiKey = $container->getParameter('pimcore_system_config.services.google.simpleapikey');
            /** @phpstan-ignore-next-line */
        } elseif ($container->hasParameter('toolbox_google_service_simple_api_key')) {
            $googleSimpleApiKey = $container->getParameter('toolbox_google_service_simple_api_key');
            /** @phpstan-ignore-next-line */
        } elseif ($pimcoreGoogleSimpleApiKey !== null) {
            $googleSimpleApiKey = $pimcoreGoogleSimpleApiKey;
        }

        $container->setParameter('toolbox.google_maps.browser_api_key', $googleBrowserApiKey);
        $container->setParameter('toolbox.google_maps.simple_api_key', $googleSimpleApiKey);
    }

    private function validateToolboxContextConfig(array $config): void
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

    private function parseContextConfigs(array $configs): array
    {
        $data = [];
        $rootConfigs = [];
        $contextConfigData = [];
        $contextMergeCandidates = [];

        foreach ($configs as $rootConfig) {
            unset($rootConfig['context'], $rootConfig['context_resolver'], $rootConfig['enabled_core_areas']);
            $rootConfigs[] = $rootConfig;
        }

        foreach ($configs as $config) {

            if (!isset($config['context'])) {
                continue;
            }

            foreach ($config['context'] as $contextName => $contextConfig) {
                $contextMergeCandidates[$contextName] = $contextConfig['settings']['merge_with_root'] ?? false;
            }
        }

        //get context data
        foreach ($configs as $config) {

            if (!isset($config['context'])) {
                continue;
            }

            foreach ($config['context'] as $contextName => $contextConfig) {

                if ($contextMergeCandidates[$contextName] === false) {
                    continue;
                }

                $cleanContextConfig = $contextConfig;

                unset($cleanContextConfig['settings']);

                $contextConfigData[$contextName][] = $cleanContextConfig;
            }
        }

        // get context merge data
        foreach ($contextMergeCandidates as $contextName => $merge) {

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

        // append merge data
        foreach ($data as $append) {
            $configs[] = $append;
        }

        // append custom context data
        foreach ($contextConfigData as $contextName => $contextConfigs) {
            foreach ($contextConfigs as $el) {
                $configs[] = [
                    'context' => [
                        $contextName => $el
                    ]
                ];
            }
        }

        return $configs;
    }
}
