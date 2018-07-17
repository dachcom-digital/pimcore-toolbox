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
        return 'context_a';
    }
}
