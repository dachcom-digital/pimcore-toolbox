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
            new \Twig_SimpleFunction('toolbox_document_tag', [$this, 'renderTag'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new \Twig_SimpleFunction('pimcore_iterate_block', [$this, 'getBlockIterator'])
        ];
    }
}