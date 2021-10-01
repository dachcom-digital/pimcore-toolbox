<?php

namespace ToolboxBundle;

final class ToolboxConfig
{
    public const TOOLBOX_TYPES = [
        'accordion',
        'anchor',
        'columns',
        'container',
        'content',
        'download',
        'gallery',
        'googleMap',
        'headline',
        'iFrame',
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

    public const CORE_TYPES = [
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

    public const CUSTOM_TYPES = [
        'additionalClasses',
        'additionalClassesChained',
        'parallaximage',
        'googlemap',
        'vhs',
    ];
}
