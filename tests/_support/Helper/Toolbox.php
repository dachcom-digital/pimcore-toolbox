<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Module;
use Dachcom\Codeception\Util\VersionHelper;

class Toolbox extends Module
{
    /**
     * API Function to create area elements for toolbox.
     *
     * @return array
     */
    public function haveToolboxAreaEditables()
    {
        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $blockAreaClass = 'Pimcore\Model\Document\Editable\Areablock';
            $selectClass = 'Pimcore\Model\Document\Editable\Select';
            $inputClass = 'Pimcore\Model\Document\Editable\Input';
        } else {
            $blockAreaClass = 'Pimcore\Model\Document\Tag\Areablock';
            $selectClass = 'Pimcore\Model\Document\Tag\Select';
            $inputClass = 'Pimcore\Model\Document\Tag\Input';
        }

        $blockArea = new $blockAreaClass();
        $blockArea->setName('dachcomBundleTest');
        $blockArea->setDataFromEditmode([
            [
                'key'    => '1',
                'type'   => 'headline',
                'hidden' => false
            ]
        ]);

        $headlineType = new $selectClass();
        $headlineType->setDataFromResource('h6');
        $headlineType->setName('dachcomBundleTest:1.headline_type');

        $headlineText = new $inputClass();
        $headlineText->setDataFromResource('this is a headline');
        $headlineText->setName('dachcomBundleTest:1.headline_text');

        $anchorName = new $inputClass();
        $anchorName->setDataFromResource('this is a anchor name');
        $anchorName->setName('dachcomBundleTest:1.anchor_name');

        $addClasses = new $selectClass();
        $addClasses->setDataFromResource(null);
        $addClasses->setName('dachcomBundleTest:1.add_classes');

        return [
            'dachcomBundleTest'                 => $blockArea,
            'dachcomBundleTest:1.headline_type' => $headlineType,
            'dachcomBundleTest:1.headline_text' => $headlineText,
            'dachcomBundleTest:1.anchor_name'   => $anchorName,
            'dachcomBundleTest:1.add_classes'   => $addClasses,
        ];
    }
}
