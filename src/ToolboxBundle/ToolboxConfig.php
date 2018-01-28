<?php

namespace ToolboxBundle;

final class ToolboxConfig
{
    const TOOLBOX_TYPES = [
        'accordion',
        'anchor',
        'columns',
        'container',
        'content',
        'download',
        'gallery',
        'googleMap',
        'headline',
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

    const CORE_TYPES = [
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
        'renderlet',
        'select',
        'snippet',
        'table',
        'textarea',
        'video',
        'wysiwyg'
    ];

    const CUSTOM_TYPES = [
        'additionalClasses',
        'parallaximage',
        'googlemap',
        'vhs',
        'dynamiclink',
    ];
}
