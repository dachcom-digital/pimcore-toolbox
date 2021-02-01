<?php

namespace ToolboxBundle\Document\Areabrick\Download;

use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\Model\Document\Tag\Relations;
use ToolboxBundle\Connector\BundleConnector;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;
use Pimcore\Model\Asset;

class Download extends AbstractAreabrick
{
    /**
     * @var BundleConnector
     */
    protected $bundleConnector;

    /**
     * @param BundleConnector $bundleConnector
     */
    public function __construct(BundleConnector $bundleConnector)
    {
        $this->bundleConnector = $bundleConnector;
    }

    /**
     * {@inheritdoc}
     */
    public function action(Info $info)
    {
        parent::action($info);

        /** @var Relations $downloadField */
        $downloadField = $this->getDocumentTag($info->getDocument(), 'relations', 'downloads');

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

        $info->getView()->getParameters()->add([
            'downloads' => $assets
        ]);

        return null;
    }

    /**
     * @param Asset $node
     *
     * @return Asset[]
     */
    protected function getByFile(Asset $node)
    {
        if (!$this->hasMembersBundle()) {
            return [$node];
        }

        /** @var \MembersBundle\Restriction\ElementRestriction $elementRestriction */
        $elementRestriction = $this->bundleConnector->getBundleService(\MembersBundle\Manager\RestrictionManager::class)->getElementRestrictionStatus($node);
        if ($elementRestriction->getSection() === \MembersBundle\Manager\RestrictionManager::RESTRICTION_SECTION_ALLOWED) {
            return [$node];
        }

        return [];
    }

    /**
     * @param Asset\Folder $node
     *
     * @return Asset[]
     */
    protected function getByFolder(Asset\Folder $node)
    {
        $assetListing = new Asset\Listing();
        $fullPath = rtrim($node->getFullPath(), '/') . '/';
        $assetListing->addConditionParam('path LIKE ?', $fullPath . '%');
        $assetListing->addConditionParam('type != ?', 'folder');

        if ($this->hasMembersBundle()) {
            $assetListing->onCreateQuery(function (QueryBuilder $query) use ($assetListing) {
                $this->bundleConnector->getBundleService(\MembersBundle\Security\RestrictionQuery::class)
                    ->addRestrictionInjection($query, $assetListing, 'assets.id');
            });
        }

        $assetListing->setOrderKey('filename');
        $assetListing->setOrder('asc');

        return $assetListing->getAssets();
    }

    /**
     * @return bool
     */
    protected function hasMembersBundle()
    {
        return $this->bundleConnector->hasBundle('MembersBundle\MembersBundle');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Downloads';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Downloads';
    }
}
