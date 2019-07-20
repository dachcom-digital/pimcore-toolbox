<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Document;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CkEditorExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('toolbox_get_ckeditor_config_path', [$this, 'getConfigPath'], [
                'needs_context' => true
            ])
        ];
    }

    /**
     * @param array $context
     *
     * @return string
     */
    public function getConfigPath(array $context)
    {
        $document = $context['document'];
        $documentId = 0;
        if ($document instanceof Document) {
            $documentId = $document->getId();
        }

        return sprintf('/admin/toolbox-ckeditor-style.js?tb_document_request_id=%s', $documentId);
    }
}
