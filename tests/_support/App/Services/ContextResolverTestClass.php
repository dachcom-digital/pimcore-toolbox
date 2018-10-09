<?php

namespace DachcomBundle\Test\App\Services;

use ToolboxBundle\Resolver\ContextResolverInterface;

/**
 * @group dataTypeOut
 */
class ContextResolverTestClass implements ContextResolverInterface
{
    public function getCurrentContextIdentifier()
    {
        $requestStack = \Pimcore::getContainer()->get('request_stack');
        $mainRequest = $requestStack->getMasterRequest();

        if($mainRequest->query->has('mock_toolbox_context')) {
            return $mainRequest->query->get('mock_toolbox_context');
        }

        return 'context_a';
    }
}
