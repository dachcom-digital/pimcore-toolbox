<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use ToolboxBundle\Connector\BundleConnector;

class MembersBundlePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('MembersBundle\Manager\RestrictionManager')) {
            return;
        }

        $toolboxBundleConnector = $container->getDefinition(BundleConnector::class);
        foreach ($this->getRequiredServices() as $service) {
            $toolboxBundleConnector->addMethodCall(
                'registerBundleService',
                [$service, new Reference($service)]
            );
        }
    }

    /**
     * @return array
     */
    private function getRequiredServices()
    {
        return [
            \MembersBundle\Manager\RestrictionManager::class,
            \MembersBundle\Security\RestrictionUri::class,
            \MembersBundle\Security\RestrictionQuery::class,
        ];
    }
}
