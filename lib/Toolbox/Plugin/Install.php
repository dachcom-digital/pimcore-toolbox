<?php

namespace Toolbox\Plugin;


class Install {

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

                $element = \Pimcore\Model\Element\Service::getElementByPath($type, $space["path"]);

                if ($element) {

                    $className = "\\Pimcore\\Model\\User\\Workspace\\" . ucfirst($type);
                    $workspace = new $className();
                    $workspace->setValues($space);

                    $workspace->setCid($element->getId());
                    $workspace->setCpath($element->getFullPath());
                    $workspace->setUserId($user->getId());

                    $newWorkspaces[] = $workspace;
                }
            }

            $user->{"setWorkspaces" . ucfirst($type)}($newWorkspaces);
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