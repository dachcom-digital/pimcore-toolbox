<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Module;
use Codeception\TestInterface;
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
        $areaElement = $this->createToolboxArea();
        $document->setElements($areaElement);

        try {
            $document->save();
        } catch (\Exception $e) {
            \Codeception\Util\Debug::debug(sprintf('[TOOLBOX ERROR] error while saving document. message was: ' . $e->getMessage()));
        }

        $this->assertCount(count($areaElement), $document->getElements());
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
        $blockArea = new Document\Tag\Areablock();
        $blockArea->setName('dachcomBundleTest');
        $headlineType = new Document\Tag\Select();
        $headlineType->setDataFromResource('h6');
        $headlineType->setName('dachcomBundleTest:1.headline_type');

        $headlineText = new Document\Tag\Input();
        $headlineText->setDataFromResource('this is a headline');
        $headlineText->setName('dachcomBundleTest:1.headline_text');

        $blockArea->setDataFromEditmode([
            [
                'key'    => '1',
                'type'   => 'headline',
                'hidden' => false
            ]
        ]);

        $data = [
            'dachcomBundleTest'                 => $blockArea,
            'dachcomBundleTest.1.headline_type' => $headlineType,
            'dachcomBundleTest.1.headline_text' => $headlineText
        ];

        return $data;
    }
}
