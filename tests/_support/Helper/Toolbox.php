<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Module;
use Dachcom\Codeception\Util\SystemHelper;
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
        $blockArea->setName(SystemHelper::AREA_TEST_NAMESPACE);
        $blockArea->setDataFromEditmode([
            [
                'key'    => '1',
                'type'   => 'headline',
                'hidden' => false
            ]
        ]);

        $headlineType = new $selectClass();
        $headlineType->setDataFromResource('h6');
        $headlineType->setName(sprintf('%s:1.headline_type', SystemHelper::AREA_TEST_NAMESPACE));

        $headlineText = new $inputClass();
        $headlineText->setDataFromResource('this is a headline');
        $headlineText->setName(sprintf('%s:1.headline_text', SystemHelper::AREA_TEST_NAMESPACE));

        $anchorName = new $inputClass();
        $anchorName->setDataFromResource('this is a anchor name');
        $anchorName->setName(sprintf('%s:1.anchor_name', SystemHelper::AREA_TEST_NAMESPACE));

        $addClasses = new $selectClass();
        $addClasses->setDataFromResource(null);
        $addClasses->setName(sprintf('%s:1.add_classes', SystemHelper::AREA_TEST_NAMESPACE));

        return [
            $blockArea->getName()    => $blockArea,
            $headlineType->getName() => $headlineType,
            $headlineText->getName() => $headlineText,
            $anchorName->getName()   => $anchorName,
            $addClasses->getName()   => $addClasses,
        ];
    }
}
