<?php

namespace ToolboxBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DocumentTagExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_document_tag', [$this, 'renderTag'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new TwigFunction('pimcore_iterate_block', [$this, 'getBlockIterator'])
        ];
    }
}
