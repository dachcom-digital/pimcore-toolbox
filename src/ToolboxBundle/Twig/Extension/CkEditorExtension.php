<?php

namespace ToolboxBundle\Twig\Extension;

use Pimcore\Model\Document;

class CkEditorExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('toolbox_get_ckeditor_config_path', [$this, 'getConfigPath'], [
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
