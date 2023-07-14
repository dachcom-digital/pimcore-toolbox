<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Editable\Select;

class AccordionTest extends AbstractAreaTest
{
    public const TYPE = 'accordion';

    public function testAccordionBackendConfig()
    {
        $this->setupRequest();

        $configElements = $this->generateBackendArea(self::TYPE);

        $this->assertCount(2, $configElements);
        $this->assertEquals('select', $configElements[0]['type']);
        $this->assertEquals('type', $configElements[0]['name']);

        $this->assertEquals('select', $configElements[1]['type']);
        $this->assertEquals('component', $configElements[1]['name']);
    }

    public function testAccordion()
    {
        $this->setupRequest();

        $component = new Select();
        $component->setDataFromResource(self::TYPE);

        $elements = [
            'component' => $component
        ];

        $this->assertEquals(
            $this->filter($this->getCompareDefault()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxAccordionId' => 'test']))
        );
    }

    public function testAccordionWithAdditionalClass()
    {
        $this->setupRequest();

        $component = new Select();
        $component->setDataFromResource('accordion');

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'component' => $component,
            'add_classes' => $combo,
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxAccordionId' => 'test']))
        );
    }

    public function testAccordionTabs()
    {
        $this->setupRequest();

        $component = new Select();
        $component->setDataFromResource('tab');

        $elements = [
            'component' => $component
        ];

        $this->assertEquals(
            $this->filter($this->getCompareTabs()),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements, ['toolboxAccordionId' => 'test']))
        );
    }

    private function getCompareDefault()
    {
        return '
            <div class="toolbox-element toolbox-accordion component-accordion ">
                <div class="accordion" id="test" role="tablist" aria-multiselectable="true">
                    <div class="card "> 
                        <div class="card-header" role="tab" id="test-1">
                            <span class="mb-0">
                                <a href="#collapse-test-1" data-target="#collapse-test-1" class="accordion-toggle collapsed" data-toggle="collapse"></a>
                            </span>
                        </div>
                        <div id="collapse-test-1" role="tabpanel" aria-labelledby="test-1" class="collapse " data-parent="#test">
                            <div class="card-body"></div>
                        </div>
                    </div>
                    <div class="card ">
                        <div class="card-header" role="tab" id="test-2">
                            <span class="mb-0">
                                <a href="#collapse-test-2" data-target="#collapse-test-2" class="accordion-toggle collapsed" data-toggle="collapse"></a>
                            </span>
                        </div>
                        <div id="collapse-test-2" role="tabpanel" aria-labelledby="test-2" class="collapse " data-parent="#test">
                            <div class="card-body"></div>
                        </div>
                    </div>
                </div>
            </div>';
    }

    private function getCompareWithAdditionalClass()
    {
        return '
            <div class="toolbox-element toolbox-accordion component-accordion additional-class">
                <div class="accordion" id="test" role="tablist" aria-multiselectable="true">
                    <div class="card "> 
                        <div class="card-header" role="tab" id="test-1">
                            <span class="mb-0">
                                <a href="#collapse-test-1" data-target="#collapse-test-1" class="accordion-toggle collapsed" data-toggle="collapse"></a>
                            </span>
                        </div>
                        <div id="collapse-test-1" role="tabpanel" aria-labelledby="test-1" class="collapse " data-parent="#test">
                            <div class="card-body"></div>
                        </div>
                    </div>
                    <div class="card ">
                        <div class="card-header" role="tab" id="test-2">
                            <span class="mb-0">
                                <a href="#collapse-test-2" data-target="#collapse-test-2" class="accordion-toggle collapsed" data-toggle="collapse"></a>
                            </span>
                        </div>
                        <div id="collapse-test-2" role="tabpanel" aria-labelledby="test-2" class="collapse " data-parent="#test">
                            <div class="card-body"></div>
                        </div>
                    </div>
                </div>
            </div>';
    }

    private function getCompareTabs()
    {
        return '
            <div class="toolbox-element toolbox-accordion component-tab ">
                <div class="tabs" id="test">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class=" nav-item">
                            <a href="#panel-test-1" id="tab-test-1" class="nav-link active" aria-controls="panel-test-1" role="tab" data-toggle="tab" aria-expanded="true"></a>
                        </li>
                        <li class=" nav-item">
                            <a href="#panel-test-2" id="tab-test-2" class="nav-link " aria-controls="panel-test-2" role="tab" data-toggle="tab" aria-expanded="false"></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade show active" id="panel-test-1" aria-labelledby="tab-test-1"></div>
                        <div role="tabpanel" class="tab-pane fade " id="panel-test-2" aria-labelledby="tab-test-2"></div>
                    </div>
                </div>
            </div>';
    }
}
