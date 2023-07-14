<?php

namespace DachcomBundle\Test\Support\Services;

use ToolboxBundle\Resolver\ContextResolverInterface;

class ContextResolverTestClass implements ContextResolverInterface
{
    public function getCurrentContextIdentifier(): ?string
    {
        $requestStack = \Pimcore::getContainer()->get('request_stack');
        $mainRequest = $requestStack->getMainRequest();

        if ($mainRequest->query->has('mock_toolbox_context')) {
            if ($mainRequest->query->get('mock_toolbox_context') === 'disabled') {
                return null;
            }

            return $mainRequest->query->get('mock_toolbox_context');
        }

        return 'context_a';
    }
}
