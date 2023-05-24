<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Connector\BundleConnector;

class MembersBundlePass implements CompilerPassInterface
{
    /**
     * @throws \Exception
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('MembersBundle\Manager\RestrictionManager')) {
            return;
        }

        $requiredServices = [
            \MembersBundle\Manager\RestrictionManager::class,
            \MembersBundle\Security\RestrictionUri::class,
            \MembersBundle\Security\RestrictionQuery::class,
        ];

        $toolboxBundleConnector = $container->getDefinition(BundleConnector::class);
        $toolboxBundleConnector->addMethodCall('addActiveBundle', ['MembersBundle']);

        foreach ($requiredServices as $service) {
            $toolboxBundleConnector->addMethodCall('registerBundleService', [$service, new Reference($service)]);
        }
    }
}
