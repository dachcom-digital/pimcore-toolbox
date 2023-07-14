<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Select;
use Pimcore\Model\Document\Editable\Snippet;
use Pimcore\Model\Document;

class TeaserSnippetTest extends AbstractAreaTest
{
    public const TYPE = 'teaser';

    /**
     * @throws \Exception
     */
    public function testTeaserWithSnippet()
    {
        $this->setupRequest();

        $elements = $this->getDefaultElements();

        $this->assertEquals(
            $this->filter($this->getCompare()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getDefaultElements()
    {
        $combo = new Document\Editable\Select();
        $combo->setDataFromResource('default');
        $combo->setName('ts_type');

        $document = new Document\Snippet();
        $document->setController('ToolboxBundle\Controller\SnippetController::teaserAction');
        $document->setEditables(['ts_type' => $combo]);
        $document->setType('snippet');
        $document->setParentId(1);
        $document->setUserOwner(1);
        $document->setUserModification(1);
        $document->setKey('snippet-test');
        $document->setPublished(true);
        $document->save();

        $type = new Select();
        $type->setDataFromResource('snippet');

        $snippet = new Snippet();
        $snippet->setDataFromResource($document->getId());

        return [
            'type'            => $type,
            'teaser-standard' => $snippet
        ];
    }

    /**
     * @return string
     */
    private function getCompare()
    {
        return '<div class="toolbox-element toolbox-teaser ">
                    <div class="row">
                        <div class="col-12">
                            <div class="single-teaser default ">
                                <h3 class="teaser-headline"></h3>
                                <div class="teaser-text"></div>
                            </div>
                        </div>
                    </div>
                </div>';
    }
}
