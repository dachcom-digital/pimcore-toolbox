<?php

namespace ToolboxBundle\Resolver;

class ContextResolver implements ContextResolverInterface
{
    public function getCurrentContextIdentifier(): ?string
    {
        return null;
    }
}
