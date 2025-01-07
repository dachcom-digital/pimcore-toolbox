<?php

namespace ToolboxBundle\EventListener\Admin;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetListener implements EventSubscriberInterface
{
    public function __construct(protected string $enabledWysiwygEditorName)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::EDITMODE_CSS_PATHS => 'addEditModeCssFiles',
            BundleManagerEvents::JS_PATHS           => 'addJsFiles',
            BundleManagerEvents::EDITMODE_JS_PATHS  => 'addEditModeJsFiles'
        ];
    }

    public function addEditModeCssFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/toolbox/css/admin.css',
            '/bundles/toolbox/css/admin_uikit.css'
        ]);
    }

    public function addJsFiles(PathsEvent $event): void
    {
        $event->addPaths(
            array_merge(
                $this->getEditorJsResources(),
                [
                    '/admin/toolbox-wysiwyg-object-style.js',
                    '/bundles/toolbox/js/document/edit.js'
                ]
            )
        );
    }

    public function addEditModeJsFiles(PathsEvent $event): void
    {
        $event->addPaths(
            array_merge(
                $this->getEditorJsResources(),
                [
                    '/bundles/toolbox/js/document/editables/area.js',
                    '/bundles/toolbox/js/document/editables/areablock.js',
                    '/bundles/toolbox/js/document/editables/googlemap.js',
                    '/bundles/toolbox/js/document/editables/parallaximage.js',
                    '/bundles/toolbox/js/document/editables/columnadjuster.js',
                    '/bundles/toolbox/js/document/editables/vhs.js',
                    '/bundles/toolbox/js/document/editables/vhs/editor.js',
                ]
            )
        );
    }

    private function getEditorJsResources(): array
    {
        return match ($this->enabledWysiwygEditorName) {
            'tiny_mce' => [
                '/bundles/toolbox/js/tinymce/google-opt-out-button.js'
            ],
            'quill' => [],
            default => []
        };
    }
}
