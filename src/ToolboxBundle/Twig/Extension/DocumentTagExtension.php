<?php

namespace ToolboxBundle\Twig\Extension;

class DocumentTagExtension extends \Pimcore\Twig\Extension\DocumentTagExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('toolbox_document_tag', [$this, 'renderTag'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new \Twig_Function('pimcore_iterate_block', [$this, 'getBlockIterator'])
        ];
    }
}