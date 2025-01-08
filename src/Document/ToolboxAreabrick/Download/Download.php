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

namespace ToolboxBundle\Document\ToolboxAreabrick\Download;

use Doctrine\DBAL\Query\QueryBuilder;
use Pimcore\Model\Asset;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Relations;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Connector\BundleConnector;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class Download extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function __construct(protected BundleConnector $bundleConnector)
    {
    }

    public function action(Info $info): ?Response
    {
        $this->buildInfoParameters($info);

        return parent::action($info);
    }

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->buildInfoParameters($info, $headlessResponse);

        parent::headlessAction($info, $headlessResponse);
    }

    protected function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        /** @var Relations $downloadField */
        $downloadField = $this->getDocumentEditable($info->getDocument(), 'relations', 'downloads');

        $assets = [];
        if (!$downloadField->isEmpty()) {
            /** @var Asset $node */
            foreach ($downloadField->getElements() as $node) {
                if ($node instanceof Asset\Folder) {
                    $assets = array_merge($assets, $this->getByFolder($node));
                } else {
                    $assets = array_merge($assets, $this->getByFile($node));
                }
            }
        }

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->addAdditionalConfigData('downloads', $assets);

            return;
        }

        $info->setParam('downloads', $assets);
    }

    protected function getByFile(Asset $node): array
    {
        if (!$this->bundleConnector->hasBundle('MembersBundle')) {
            return [$node];
        }

        /** @var \MembersBundle\Restriction\ElementRestriction $elementRestriction */
        $elementRestriction = $this->bundleConnector->getBundleService(\MembersBundle\Manager\RestrictionManager::class)->getElementRestrictionStatus($node);
        if ($elementRestriction->getSection() === \MembersBundle\Manager\RestrictionManager::RESTRICTION_SECTION_ALLOWED) {
            return [$node];
        }

        return [];
    }

    protected function getByFolder(Asset\Folder $node): array
    {
        $assetListing = new Asset\Listing();
        $fullPath = rtrim($node->getFullPath(), '/') . '/';
        $assetListing->addConditionParam('path LIKE ?', $fullPath . '%');
        $assetListing->addConditionParam('type != ?', 'folder');

        if ($this->bundleConnector->hasBundle('MembersBundle')) {
            $assetListing->onCreateQueryBuilder(function (QueryBuilder $query) use ($assetListing) {
                $this->bundleConnector
                    ->getBundleService(\MembersBundle\Security\RestrictionQuery::class)
                    ->addRestrictionInjection($query, $assetListing, 'id');
            });
        }

        $assetListing->setOrderKey('filename');
        $assetListing->setOrder('asc');

        return $assetListing->getAssets();
    }

    public function getName(): string
    {
        return 'Downloads';
    }

    public function getDescription(): string
    {
        return 'Toolbox Downloads';
    }
}
