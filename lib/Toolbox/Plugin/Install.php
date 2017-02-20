<?php

namespace Toolbox\Plugin;

use Pimcore\Model\Translation\Admin;

class Install
{
    /**
     * @var null|string
     */
    private $configFile = NULL;

    /**
     * Install constructor.
     */
    public function __construct()
    {
        $this->configFile = TOOLBOX_CONFIGURATION_FILE;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        $userM = new \Pimcore\Model\User();
        $user = $userM->getByName('kunde');

        return $user !== FALSE && is_file($this->configFile);
    }

    /**
     *
     */
    public function installAdminTranslations()
    {
        $csv = PIMCORE_PLUGINS_PATH . '/Toolbox/install/translations/data.csv';
        Admin::importTranslationsFromFile($csv, TRUE, \Pimcore\Tool\Admin::getLanguages());
    }

    /**
     *
     */
    public function installConfigFile()
    {
        if (!is_file($this->configFile)) {

            $settings = [

                "accordion"         => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "type",
                            "title"   => "Type",
                            "values"  => [
                                "panel-default" => "Default",
                                "panel-danger"  => "Dangers"
                            ],
                            "default" => "panel-default"
                        ],
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "columns"           => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "type",
                            "title"   => "Columns",
                            "values"  => [
                                "column_12"    => "1 Column",
                                "column_4_8"   => "2 Columns (33:66)",
                                "column_8_4"   => "2 Columns (66:33)",
                                "column_3_9"   => "2 Columns (25:75)",
                                "column_9_3"   => "2 Columns (75:25)",
                                "column_6_6"   => "2 Columns (50:50)",
                                "column_4_4_4" => "3 Columns (33:33:33)"
                            ],
                            "default" => "column_12"
                        ],
                        [
                            "type"   => "checkbox",
                            "name"   => "equalHeight",
                            "title"  => "Equal heights?",
                            "reload" => FALSE

                        ]
                    ],
                ],
                "slideColumns"      => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "slidesPerView",
                            "title"   => "Slides per View",
                            "values"  => [
                                "2" => "2 Columns",
                                "3" => "3 Columns",
                                "4" => "4 Columns",
                                "6" => "6 Columns",
                            ],
                            "default" => "4"
                        ],
                        [
                            "type"   => "checkbox",
                            "name"   => "equalHeight",
                            "title"  => "Equal heights?",
                            "reload" => FALSE
                        ]
                    ],
                    "columnClasses"  => [
                        "2" => "col-xs-12 col-sm-6"
                    ],
                    "breakpoints"    => [

                    ]
                ],
                "content"           => [
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "download"          => [
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ],
                    ],

                ],
                "gallery"           => [
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "headline"          => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "headlineType",
                            "title"   => "Headline Size",
                            "values"  => [
                                "h1" => "Headline 1",
                                "h2" => "Headline 2",
                                "h3" => "Headline 3",
                                "h4" => "Headline 4",
                                "h5" => "Headline 5",
                                "h6" => "Headline 6"
                            ],
                            "default" => "h3"
                        ],
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],
                ],
                "image"             => [
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "linklist"          => [
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "parallaxContainer" => [
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "separator"         => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "space",
                            "title"   => "Space before & after separator",
                            "values"  => [
                                "default" => "Default",
                                "medium"  => "Medium",
                                "large"   => "Large"
                            ],
                            "default" => "default"
                        ],
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "spacer"            => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "spacerClass",
                            "title"   => "Space Class",
                            "values"  => [
                                "spacer-none" => "No Space",
                                "spacer-50"   => "50 Pixel"
                            ],
                            "default" => "spacer-none",
                            "reload" => FALSE
                        ],
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "teaser"            => [
                    "configElements" => [
                        [
                            "type"    => "select",
                            "name"    => "type",
                            "title"   => "Type",
                            "values"  => [
                                "direct"  => "Direct",
                                "snippet" => "Snippet"
                            ],
                            "default" => "direct"
                        ],
                        [
                            "type"       => "select",
                            "name"       => "layout",
                            "title"      => "Layout",
                            "values"     => [
                                "default" => "Default"
                            ],
                            "default"    => "default",
                            "conditions" => [
                                ["type" => "direct"]
                            ]
                        ],
                        [
                            "type"  => "checkbox",
                            "name"  => "useLightBox",
                            "title" => "use Lightbox?",

                        ],
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ]
                    ],

                ],
                "video"             => [
                    "videoOptions"   => [
                        "youtube" => [
                            "posterImageThumbnail" => NULL,
                        ]
                    ],
                    "configElements" => [
                        [
                            "type"   => "additionalClasses",
                            "values" => [],
                        ],
                        [
                            "type"   => "checkbox",
                            "name"   => "autoplay",
                            "title"  => "Autoplay?",
                            "reload" => FALSE
                        ],
                    ],
                ],

                "googleMap" => [
                    "configElements" => [],
                    "mapOptions"     => [
                        "streetViewControl" => TRUE,
                        "mapTypeControl"    => FALSE,
                        "panControl"        => FALSE,
                        "scrollwheel"       => FALSE,
                    ],
                    "mapStyleUrl"    => FALSE,
                    "markerIcon"     => FALSE
                ],

                "ckeditor" => [

                    "globalStyleSets" => [
                        "default" => [

                            [
                                "name"       => "Button Default",
                                "element"    => "a",
                                "attributes" => [
                                    "class" => "btn btn-default"
                                ]
                            ]
                        ],
                    ],

                    "areaEditor"   => [
                        "stylesSet" => [

                        ],
                    ],
                    "objectEditor" => [
                        "stylesSet" => [

                        ],
                    ]
                ],

                "disallowedSubAreas" => [
                    "accordion"         => ["accordion", "container", "slideColumns"],
                    "columns"           => ["container", "parallaxContainer"],
                    "slideColumns"      => ["slideColumns", "accordion", "container", "columns", "anchor"],
                    "container"         => ["container", "parallaxContainer"],
                    "parallaxContainer" => ["parallaxContainer"],
                    "image"             => ["parallaxContainer"],
                    "snippet"           => ["parallaxContainer"]
                ],

                "disallowedContentSnippetAreas" => [
                    "parallaxContainer",
                    "teaser",
                    "container",
                    "snippet"
                ],

                "areaBlockConfiguration" => [

                    "toolbar" => [

                        "title"       => "Inhaltsbausteine",
                        "width"       => 200,
                        "x"           => 10,
                        "y"           => 125,
                        "buttonWidth" => 200

                    ],

                    "groups" => FALSE

                ]
            ];

            \Pimcore\File::putPhpFile($this->configFile, to_php_data_file_format($settings));
        }
    }

    /**
     * Adds an default customer user role & user itself
     */
    public function addUserData()
    {
        $userRole = $this->installUserRole();
        $this->installUser($userRole);
    }

    /**
     * @return \Pimcore\Model\User\AbstractUser
     */
    private function installUserRole()
    {
        $userRole = new \Pimcore\Model\User\Role();
        $customerRole = $userRole->getByName('kunde');

        if ($customerRole !== FALSE) {
            return $customerRole;
        }

        $user = \Pimcore\Model\User\Role::create(
            [

                'parentId' => 0,
                'name'     => 'kunde',
                'active'   => 1

            ]
        );

        $permissions = [

            'assets'            => TRUE,
            'plugin_coreshop'   => TRUE,
            'coreshop_country'  => TRUE,
            'coreshop_currency' => TRUE,
            'coreshop_zone'     => TRUE,
            'coreshop_user'     => TRUE,
            'dashboards'        => TRUE,
            'documents'         => TRUE,
            'notes_events'      => TRUE,
            'objects'           => TRUE,
            'recyclebin'        => TRUE,
            'redirects'         => TRUE,
            'seemode'           => TRUE,
            'users'             => TRUE,
            'website_settings'  => TRUE

        ];

        $classes = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        $workspaces = [
            'document' => [
                [
                    'path'       => '/',
                    'list'       => TRUE,
                    'save'       => TRUE,
                    'unpublish'  => TRUE,
                    'view'       => TRUE,
                    'publish'    => TRUE,
                    'delete'     => TRUE,
                    'rename'     => TRUE,
                    'create'     => TRUE,
                    'settings'   => TRUE,
                    'versions'   => TRUE,
                    'properties' => TRUE
                ],
                [
                    'path' => '/demo'
                ]
            ],
            'object'   => [
                [
                    'path'       => '/',
                    'list'       => TRUE,
                    'save'       => TRUE,
                    'unpublish'  => TRUE,
                    'view'       => TRUE,
                    'publish'    => TRUE,
                    'delete'     => TRUE,
                    'rename'     => TRUE,
                    'create'     => TRUE,
                    'settings'   => TRUE,
                    'versions'   => TRUE,
                    'properties' => TRUE
                ]
            ],
            'asset'    => [
                [
                    'path'       => '/',
                    'list'       => TRUE,
                    'save'       => TRUE,
                    'unpublish'  => TRUE,
                    'view'       => TRUE,
                    'publish'    => TRUE,
                    'delete'     => TRUE,
                    'rename'     => TRUE,
                    'create'     => TRUE,
                    'settings'   => TRUE,
                    'versions'   => TRUE,
                    'properties' => TRUE
                ]
            ]

        ];

        foreach ($workspaces as $type => $spaces) {

            $newWorkspaces = [];

            foreach ($spaces as $space) {

                $element = \Pimcore\Model\Element\Service::getElementByPath($type, $space['path']);

                if ($element) {

                    $className = '\\Pimcore\\Model\\User\\Workspace\\' . ucfirst($type);
                    $workspace = new $className();
                    $workspace->setValues($space);

                    $workspace->setCid($element->getId());
                    $workspace->setCpath($element->getFullPath());
                    $workspace->setUserId($user->getId());

                    $newWorkspaces[] = $workspace;
                }
            }

            $user->{'setWorkspaces' . ucfirst($type)}($newWorkspaces);
        }

        foreach ($permissions as $permName => $permAccess) {

            $user->setPermission($permName, $permAccess);
        }

        $user->setDocTypes(implode(',', [1]));

        $user->setClasses(implode(',', $classes));

        $user->save();

        return $user;
    }

    /***
     * @param \Pimcore\Model\User\Role $userRole
     *
     * @return \Pimcore\Model\User\AbstractUser
     */
    private function installUser(\Pimcore\Model\User\Role $userRole)
    {
        $userM = new \Pimcore\Model\User();
        $user = $userM->getByName('kunde');

        if ($user !== FALSE) {
            return $user;
        }

        $user = \Pimcore\Model\User::create(

            [

                'parentId' => 0,
                'name'     => 'kunde',
                'password' => \Pimcore\Tool\Authentication::getPasswordHash('kunde', 'kunde'),
                'active'   => 1,
                'language' => 'de',
                'admin'    => FALSE,
                'roles'    => [0 => $userRole->getId()]

            ]);

        $user->save();

        return $user;
    }

}