<?php

namespace ToolboxBundle\Twig\Extension;

use Twig\TwigFunction;

class DocumentTagExtension extends \Pimcore\Twig\Extension\DocumentTagExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
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
