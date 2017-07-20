<?php

namespace ToolboxBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MembersBundlePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('members.manager.restriction')) {
            return;
        }

        $toolboxBundleConnector = $container->getDefinition('toolbox.connector.bundle');
        foreach($this->getRequiredServices() as $service) {
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
            'members.manager.restriction',
            'members.security.restriction.uri',
            'members.security.restriction.query',

        ];
    }
}