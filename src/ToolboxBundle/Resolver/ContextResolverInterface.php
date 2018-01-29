<?php

namespace ToolboxBundle\Resolver;

interface ContextResolverInterface
{
    /**
     * @return null|string
     */
    public function getCurrentContextIdentifier();
}