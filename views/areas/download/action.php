<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\ExtensionManager;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;

class Download extends Document\Tag\Area\AbstractArea
{
    /**
     *
     */
    public function action()
    {
        //check if member extension exist
        $hasMembers = $this->hasMembersExtension();

        $assets = [];
        if (!$this->getView()->multihref('downloads')->isEmpty()) {

            /** @var \Pimcore\Model\Asset $node */
            foreach ($this->getView()->multihref('downloads')->getElements() as $node) {

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

        $this->getView()->assign([
            'downloads' => $assets
        ]);
    }


    public function getBrickHtmlTagOpen($brick)
    {
        return '';
    }

    public function getBrickHtmlTagClose($brick)
    {
        return '';
    }

    /**
     * Check if members extension is available.
     * @url https://github.com/dachcom-digital/pimcore-members
     * @return bool
     */
    private function hasMembersExtension()
    {
        return ExtensionManager::isEnabled('plugin', 'Members');
    }
}