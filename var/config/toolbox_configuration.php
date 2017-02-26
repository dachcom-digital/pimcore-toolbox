<?php

return [
    "container"                => [
        "configElements" => [
            [
                "type"   => "checkbox",
                "name"   => "fullWidthContainer",
                "title"  => "Fluid Container (Full Width)",
                "reload" => TRUE
            ]
        ]
    ],
    "parallaxContainer"        => [
        "windowSize"          => "large",
        "backgroundMode"      => "wrap",
        "backgroundImageMode" => "data", //style|data
        "backgroundColorMode" => "data", //style|data|class
        "configElements"      => [
            [
                "type"    => "select",
                "name"    => "template",
                "title"   => "Parallax Template",
                "reload"  => TRUE,
                "default" => "no-template",
                "values"  => [
                    "no-template" => "No Template"
                ]
            ],
            [
                "type"     => "href",
                "name"     => "backgroundImage",
                "title"    => "Background Image",
                "types"    => ["asset"],
                "subtypes" => [
                    "asset" => ["image", "video"]
                ],
                "reload"   => TRUE
            ],
            [
                "type"    => "select",
                "name"    => "backgroundColor",
                "title"   => "Background Color",
                "reload"  => TRUE,
                "default" => "no-background-color",
                "values"  => [
                    "no-background-color" => "No Background Color"
                ]
            ],
            [
                "type"     => "parallaximage",
                "name"     => "imageFront",
                "title"    => "Images In Front Of Main Container",
                "position" => [
                    "top-left"      => "Top Left",
                    "top-center"    => "Top Center",
                    "top-right"     => "Top Right",
                    "bottom-left"   => "Bottom Left",
                    "bottom-center" => "Bottom Center",
                    "bottom-right"  => "Bottom Right",
                    "center-left"   => "Center Left",
                    "center-center" => "Center Center",
                    "center-right"  => "Center Right"
                ],
                "size"     => [
                    "half-window-width"    => "Half Window Width",
                    "third-window-width"   => "Third Window Width ",
                    "quarter-window-width" => "Quarter Window Width",
                ],
                "reload"   => TRUE
            ],
            [
                "type"     => "parallaximage",
                "name"     => "imagesBehind",
                "title"    => "Images Behind Main Container",
                "position" => [
                    "top-left"      => "Top Left",
                    "top-center"    => "Top Center",
                    "top-right"     => "Top Right",
                    "bottom-left"   => "Bottom Left",
                    "bottom-center" => "Bottom Center",
                    "bottom-right"  => "Bottom Right",
                    "center-left"   => "Center Left",
                    "center-center" => "Center Center",
                    "center-right"  => "Center Right"
                ],
                "size"     => [
                    "half-window-width"    => "Half Window Width",
                    "third-window-width"   => "Third Window Width ",
                    "quarter-window-width" => "Quarter Window Width",
                ],
                "reload"   => TRUE
            ]
        ]
    ],
    "parallaxContainerSection" => [
        "backgroundImageMode" => "data", //style|data
        "backgroundColorMode" => "data", //style|data|class
        "configElements" => [
            [
                "type"    => "select",
                "name"    => "template",
                "title"   => "Section Template",
                "reload"  => TRUE,
                "default" => "no-template",
                "values"  => [
                    "no-template" => "No Template"
                ]
            ],
            [
                "type"        => "select",
                "name"        => "containerType",
                "title"       => "Section Wrapper Type",
                "reload"      => TRUE,
                "default"     => "none",
                "description" => "If you need to add some content columns, apply a default or fluid container as wrapper.",
                "values"      => [
                    "none"            => "No Section Wrapper",
                    "container"       => "Default Container Wrapper",
                    "container-fluid" => "Fluid Container Wrapper"
                ]
            ],
            [
                "type"     => "href",
                "name"     => "backgroundImage",
                "title"    => "Background Image",
                "types"    => ["asset"],
                "subtypes" => [
                    "asset" => ["image", "video"]
                ],
                "reload"   => TRUE
            ],
            [
                "type"    => "select",
                "name"    => "backgroundColor",
                "title"   => "Background Color",
                "reload"  => TRUE,
                "default" => "no-background-color",
                "values"  => [
                    "no-background-color" => "No Background Color"
                ]
            ]
        ]
    ],
    "headline"                 => [
        "configElements" => [
            [
                "type"  => "input",
                "name"  => "anchorName",
                "title" => "Anchor Name",
            ],
        ]
    ],
    "anchor"                   => [
        "configElements" => [
            [
                "type"  => "input",
                "name"  => "anchorName",
                "title" => "Anchor Name",
            ],
            [
                "type"  => "input",
                "name"  => "anchorTitle",
                "title" => "Anchor Title",
            ],
        ]
    ],
    "image"                    => [
        "configElements" => [
            [
                "type"      => "checkbox",
                "name"      => "useLightbox",
                "title"     => "Use Lightbox",
                "col-class" => "t-col-half",
                "reload"    => FALSE
            ],
            [
                "type"      => "checkbox",
                "name"      => "showCaption",
                "title"     => "Display Caption",
                "col-class" => "t-col-half",
                "reload"    => FALSE
            ]
        ]
    ],
    "download"                 => [
        "configElements" => [
            [
                "type"   => "multihref",
                "name"   => "downloads",
                "title"  => "Files",
                "reload" => TRUE
            ],
            [
                "type"   => "checkbox",
                "name"   => "showPreviewImages",
                "title"  => "Show preview images",
                "reload" => FALSE
            ],
            [
                "type"   => "checkbox",
                "name"   => "showFileInfo",
                "title"  => "Show file info",
                "reload" => FALSE
            ]
        ]
    ],
    "gallery"                  => [
        "configElements" => [
            [
                "type"   => "multihref",
                "name"   => "images",
                "title"  => "Images or Folder",
                "reload" => TRUE
            ],
            [
                "type"   => "checkbox",
                "name"   => "useLightbox",
                "title"  => "Use Lightbox",
                "reload" => FALSE
            ],
            [
                "type"   => "checkbox",
                "name"   => "useThumbnails",
                "title"  => "Use Thumbnails",
                "reload" => FALSE
            ]
        ]
    ],
    "googleMap"                => [
        "configElements" => [
            [
                "type"             => "numeric",
                "name"             => "mapZoom",
                "title"            => "Map zoom",
                "width"            => 100,
                "minValue"         => 0,
                "maxValue"         => 19,
                "decimalPrecision" => 0,
                "default"          => 12,
                "reload"           => FALSE
            ],
            [
                "type"    => "select",
                "name"    => "mapType",
                "title"   => "Map type",
                "reload"  => TRUE,
                "default" => "roadmap",
                "values"  => [
                    "roadmap"   => "ROADMAP",
                    "satellite" => "SATELLITE",
                    "hybrid"    => "HYBRID",
                    "terrain"   => "TERRAIN"
                ]
            ]
        ]
    ],
];
