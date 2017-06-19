<?php

namespace ToolboxBundle\Document\Areabrick\Download;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

use Pimcore\Model\Asset;

class Download extends AbstractAreabrick
{
    /**
     * @todo:
     *      - check asset instanceof \Pimcore\Model\Asset
     *      - fix members binding
     *
     * @param Info $info
     */
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        //check if member extension exist
        $hasMembers = $this->hasMembersExtension();
        $downloadField = $this->getDocumentTag($info->getDocument(),'multihref', 'downloads');

        $assets = [];
        if (!$downloadField->isEmpty()) {

            /** @var \Pimcore\Model\Asset $node */
            foreach ($downloadField->getElements() as $node) {

                //it's a folder. get all sub asets!
                if($node instanceof Asset\Folder) {

                    $assetListing = new Asset\Listing();
                    $fullPath = rtrim($node->getFullPath(), '/') . '/';
                    $assetListing->addConditionParam('path LIKE ?', $fullPath . '%');

                    if($hasMembers) {
                        $assetListing->onCreateQuery(function (\Zend_Db_Select $query) use ($assetListing) {
                            \Members\Tool\Query::addRestrictionInjection($query, $assetListing, 'assets.id');
                        });
                    }

                    /** @var Asset $entry */
                    foreach ($assetListing->load() as $entry) {
                        if (!$entry instanceof Asset\Folder) {
                            $assets[] = $entry;
                        }
                    }

                    //default asset
                } else {

                    if($hasMembers) {
                        $assetRestriction = \Members\Tool\Observer::isRestrictedAsset($node);
                        if($assetRestriction['section'] === \Members\Tool\Observer::SECTION_ALLOWED) {
                            $assets[] = $node;
                        }
                    } else {
                        $assets[] = $node;
                    }

                }
            }
        }

        $view->downloads = $assets;

    }

    public function getName()
    {
        return 'Downloads';
    }

    public function getDescription()
    {
        return 'Toolbox Downloads';
    }

    /**
     * Check if members extension is available.
     * @url https://github.com/dachcom-digital/pimcore-members
     * @return bool
     */
    private function hasMembersExtension()
    {
        $hasMembers = FALSE;

        try {
            $hasMembers  = $this->container->get('pimcore.extension.bundle_manager')->isEnabled('MembersBundle\MembersBundle');
        } catch(\Exception $e) {}

        return $hasMembers;
    }

}