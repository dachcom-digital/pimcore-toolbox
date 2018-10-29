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
}
