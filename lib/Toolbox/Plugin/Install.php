<?php

namespace Toolbox\Plugin;


class Install {

    private $configFile = NULL;

    public function __construct() {

        $this->configFile = TOOLBOX_CONFIGURATION_FILE;

    }

    public function isInstalled() {

        $userM = new \Pimcore\Model\User();
        $user = $userM->getByName('kunde');

        return $user !== FALSE && is_file( $this->configFile );

    }

    public function installConfigFile() {

        if(!is_file( $this->configFile ) ) {

            $settings = array(

                'columnElements' => [
                    'column_12' => '1 Spalte',
                    'column_4_8' => '2 Spalte (33:66)',
                    'column_8_4' => '2 Spalte (66:33)',
                    'column_3_9' => '2 Spalte (25:75)',
                    'column_9_3' => '2 Spalte (75:25)',
                    'column_6_6' => '2 Spalte (50:50)',
                    'column_4_4_4' => '3 Spalte (33:33:33)',
                ],
                'accordion' => [
                    'layouts' => [
                        'panel-default' => 'Default',
                        'panel-danger' => 'Dangers'
                    ],
                    'additionalClasses' => []
                ],
                "parallaxContainer" => [
                    "additionalClasses" => [
                        "window-full-height" => "mind. Fensterhöhe",
                    ]
                ],
                'headlines' => [
                    'tags' => [
                        'h1' => 'Headline 1',
                        'h2' => 'Headline 2',
                        'h3' => 'Headline 3',
                        'h4' => 'Headline 4',
                        'h5' => 'Headline 5',
                        'h6' => 'Headline 6'
                    ],
                    'additionalClasses' => []
                ],
                'allowedPlugins' => [
                    'accordion' => TRUE,
                    'columns' => TRUE,
                    'content' => TRUE,
                    'headline' => TRUE,
                    'gallery' => TRUE,
                    'image' => TRUE,
                    'teaser' => TRUE,
                    'snippet' => TRUE,
                    'video' => TRUE,
                    'separator' => TRUE
                ]
            );

            \Pimcore\File::putPhpFile($this->configFile, to_php_data_file_format($settings));
        }

    }

    /**
     * Adds an default customer user role & user itself
     */
    public function addUserData() {

        $userRole = $this->installUserRole();

        $this->installUser($userRole);
    }

    private function installUserRole() {

        $userRole = new \Pimcore\Model\User\Role();
        $customerRole = $userRole->getByName('kunde');

        if ($customerRole !== FALSE) {
            return $customerRole;
        }

        $user = \Pimcore\Model\User\Role::create(
            array(

                'parentId' => 0,
                'name' => 'kunde',
                'active' => 1

            )
        );

        $permissions = array(

            'assets' => TRUE,
            'plugin_coreshop' => TRUE,
            'coreshop_country' => TRUE,
            'coreshop_currency' => TRUE,
            'coreshop_zone' => TRUE,
            'coreshop_user' => TRUE,
            'dashboards' => TRUE,
            'documents' => TRUE,
            'notes_events' => TRUE,
            'objects' => TRUE,
            'recyclebin' => TRUE,
            'redirects' => TRUE,
            'seemode' => TRUE,
            'users' => TRUE,
            'website_settings' => TRUE

        );

        $classes = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

        $workspaces = array(
            'document' => array(
                array(
                    'path' => '/',
                    'list' => TRUE,
                    'save' => TRUE,
                    'unpublish' => TRUE,
                    'view' => TRUE,
                    'publish' => TRUE,
                    'delete' => TRUE,
                    'rename' => TRUE,
                    'create' => TRUE,
                    'settings' => TRUE,
                    'versions' => TRUE,
                    'properties' => TRUE
                ),
                array(
                    'path' => '/demo'
                )
            ),
            'object' => array(
                array(
                    'path' => '/',
                    'list' => TRUE,
                    'save' => TRUE,
                    'unpublish' => TRUE,
                    'view' => TRUE,
                    'publish' => TRUE,
                    'delete' => TRUE,
                    'rename' => TRUE,
                    'create' => TRUE,
                    'settings' => TRUE,
                    'versions' => TRUE,
                    'properties' => TRUE
                )
            ),
            'asset' => array(
                array(
                    'path' => '/',
                    'list' => TRUE,
                    'save' => TRUE,
                    'unpublish' => TRUE,
                    'view' => TRUE,
                    'publish' => TRUE,
                    'delete' => TRUE,
                    'rename' => TRUE,
                    'create' => TRUE,
                    'settings' => TRUE,
                    'versions' => TRUE,
                    'properties' => TRUE
                )
            )

        );

        foreach ($workspaces as $type => $spaces) {

            $newWorkspaces = array();

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

        $user->setDocTypes(implode(',', array(1)));

        $user->setClasses(implode(',', $classes));

        $user->save();

        //var_dump($user);

        return $user;
    }

    private function installUser(\Pimcore\Model\User\Role $userRole) {

        $userM = new \Pimcore\Model\User();
        $user = $userM->getByName('kunde');

        if ($user !== FALSE) {
            return $user;
        }

        $user = \Pimcore\Model\User::create(

            array(

                'parentId' => 0,
                'name' => 'kunde',
                'password' => \Pimcore\Tool\Authentication::getPasswordHash('kunde', 'kunde'),
                'active' => 1,
                'language' => 'de',
                'admin' => FALSE,
                'roles' => array(0 => $userRole->getId())

            ));

        $user->save();

        return $user;

    }

}