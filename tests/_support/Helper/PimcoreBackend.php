<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use DachcomBundle\Test\Util\VersionHelper;
use Pimcore\Tests\Util\TestHelper;
use Pimcore\Model\Document;

class PimcoreBackend extends Module
{
    /**
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        parent::_before($test);

        TestHelper::cleanUpTree(Document::getById(1), 'document');
    }

    /**
     * Actor Function to create a Page Document
     *
     * @param string      $documentKey
     * @param null|string $action
     * @param null|string $controller
     * @param null|string $locale
     *
     * @return Document\Page
     */
    public function haveAPageDocument(
        $documentKey = 'toolbox-test',
        $action = null,
        $controller = null,
        $locale = 'en'
    ) {
        $document = $this->generatePageDocument($documentKey, $action, $controller, $locale);

        try {
            $document->save();
        } catch (\Exception $e) {
            \Codeception\Util\Debug::debug(sprintf('[TOOLBOX ERROR] error while saving document page. message was: ' . $e->getMessage()));
        }

        $this->assertInstanceOf(Document\Page::class, Document\Page::getById($document->getId()));

        return $document;
    }

    /**
     * Actor Function to create a Snippet
     *
     * @param       $snippetName
     * @param array $elements
     *
     */
    public function haveASnippet($snippetName, $elements = [])
    {
        $this->createSnippet($snippetName, $elements);
    }

    /**
     * Actor Function to place a toolbox area on a document
     *
     * @param Document\Page $document
     */
    public function seeAToolboxAreaElementPlacedOnDocument(Document\Page $document)
    {
        $editables = $this->createToolboxArea();

        if (VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0')) {
            $document->setEditables($editables);
        } else {
            $document->setElements($editables);
        }

        try {
            $document->save();
        } catch (\Exception $e) {
            \Codeception\Util\Debug::debug(sprintf('[TOOLBOX ERROR] error while saving document. message was: ' . $e->getMessage()));
        }

        $this->assertCount(count($editables), VersionHelper::pimcoreVersionIsGreaterOrEqualThan('6.8.0') ? $document->getEditables() : $document->getElements());
    }

    /**
     * API Function to create a page document
     *
     * @param string      $key
     * @param null|string $action
     * @param null|string $controller
     * @param string      $locale
     *
     * @return Document\Page
     */
    protected function generatePageDocument($key = 'toolbox-test', $action = null, $controller = null, $locale = 'en')
    {
        $action = is_null($action) ? 'default' : $action;
        $controller = is_null($controller) ? '@AppBundle\Controller\DefaultController' : $controller;

        $document = TestHelper::createEmptyDocumentPage('', false);
        $document->setController($controller);
        $document->setAction($action);
        $document->setKey($key);
        $document->setProperty('language', 'text', $locale, false, 1);

        return $document;
    }

    /**
     * API Function to create a Snippet
     *
     * @param       $snippetName
     * @param array $elements
     *
     * @return null|Document\Snippet
     */
    protected function createSnippet($snippetName, $elements = [])
    {
        $document = new Document\Snippet();
        $document->setModule('ToolboxBundle');
        $document->setController('Snippet');
        $document->setAction('teaser');
        $document->setType('snippet');
        $document->setElements($elements);
        $document->setParentId(1);
        $document->setUserOwner(1);
        $document->setUserModification(1);
        $document->setCreationDate(time());
        $document->setKey($snippetName);
        $document->setPublished(true);

        try {
            $document->save();
        } catch (\Exception $e) {
            $this->debug(sprintf('[PIMCORE BACKEND MODULE]: error while creating snippet: ' . $e->getMessage()));
            return null;
        }

        return $document;

    }

    /**
     * API Function to create a toolbox area element.
     *
     * @return array
     */
    protected function createToolboxArea()
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

        $blockArea = new $blockAreaClass();
        $blockArea->setName('dachcomBundleTest');
        $blockArea->setDataFromEditmode([
            [
                'key'    => '1',
                'type'   => 'headline',
                'hidden' => false
            ]
        ]);

        return [
            'dachcomBundleTest'                 => $blockArea,
            'dachcomBundleTest:1.headline_type' => $headlineType,
            'dachcomBundleTest:1.headline_text' => $headlineText,
            'dachcomBundleTest:1.anchor_name'   => $anchorName,
            'dachcomBundleTest:1.add_classes'   => $addClasses,
        ];
    }
}
