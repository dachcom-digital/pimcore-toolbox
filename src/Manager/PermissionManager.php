<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Manager;

use Pimcore\Extension\Document\Areabrick\AreabrickInterface;
use Pimcore\Extension\Document\Areabrick\AreabrickManager;
use Pimcore\Model\Translation;
use Pimcore\Model\User;
use Pimcore\Security\User\UserLoader;

class PermissionManager implements PermissionManagerInterface
{
    public function __construct(
        protected AreabrickManager $brickManager,
        protected UserLoader $userLoader
    ) {
    }

    public function synchroniseEditablePermissions(): void
    {
        $category = [
            'key'          => 'toolbox.permission.editable.category_name',
            'translations' => [
                'de' => 'Toolbox Editables',
                'en' => 'Toolbox Editables'
            ]
        ];

        if (Translation::getByKey('toolbox.permission.editable.category_name', Translation::DOMAIN_ADMIN) === null) {
            $t = new Translation();
            $t->setDomain(Translation::DOMAIN_ADMIN);
            $t->setKey($category['key']);
            $t->setCreationDate(time());
            $t->setModificationDate(time());
            foreach ($category['translations'] as $locale => $translation) {
                $t->addTranslation($locale, $translation);
            }
            $t->save();
        }

        /**
         * @var string             $editableId
         * @var AreabrickInterface $areaBrick
         */
        foreach ($this->brickManager->getBricks() as $editableId => $areaBrick) {
            $permission = $this->generatePermissionName($editableId);
            $definition = User\Permission\Definition::getByKey($permission);

            if ($definition) {
                continue;
            }

            $permissionDefinition = new User\Permission\Definition();
            $permissionDefinition->setKey($permission);
            $permissionDefinition->setCategory($category['key']);
            $permissionDefinition->save();
        }
    }

    public function getDisallowedEditables(array $editables): array
    {
        $allowedElements = [];

        foreach ($editables as $editableId) {
            if ($this->isAllowedEditable($editableId)) {
                continue;
            }

            $allowedElements[] = $editableId;
        }

        return $allowedElements;
    }

    protected function isAllowedEditable(string $editableId): bool
    {
        $user = $this->getUser();

        if ($user === null) {
            return false;
        }

        $permission = $this->generatePermissionName($editableId);

        return $user->isAllowed($permission);
    }

    protected function getUser(): ?User
    {
        $user = $this->userLoader->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    protected function generatePermissionName(string $editableId): string
    {
        return sprintf('toolbox_editable_%s', strtolower(str_replace(['-'], ['_'], $editableId)));
    }
}
