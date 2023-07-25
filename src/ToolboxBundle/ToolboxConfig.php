<?php

namespace ToolboxBundle;

final class ToolboxConfig
{
    public const TOOLBOX_AREA_TYPES = [
        'accordion',
        'anchor',
        'columns',
        'container',
        'content',
        'download',
        'gallery',
        'googleMap',
        'headline',
        'iframe',
        'image',
        'linkList',
        'parallaxContainer',
        'parallaxContainerSection',
        'separator',
        'slideColumns',
        'snippet',
        'spacer',
        'teaser',
        'video'
    ];

    public const PIMCORE_EDITABLE_TYPES = [
        'areablock',
        'area',
        'block',
        'checkbox',
        'date',
        'href',
        'image',
        'input',
        'link',
        'multihref',
        'multiselect',
        'numeric',
        'embed',
        'pdf',
        'relation',
        'relations',
        'renderlet',
        'select',
        'snippet',
        'table',
        'textarea',
        'video',
        'wysiwyg'
    ];

    public const TOOLBOX_EDITABLE_TYPES = [
        'additionalClasses',
        'additionalClassesChained',
        'parallaximage',
        'googlemap',
        'vhs',
    ];
}
