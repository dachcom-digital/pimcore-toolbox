# Headless
Learn, how to put toolbox into headless mode.

## Thinks to know
- The Headless layout will super simplify the backend edit mode. No views will be generated there, but only the configuration fields.
- In the frontend layer, the editable output will be a simple array (already nested)
- All toolbox editables except `parallax` and `slidecolumn` are headless ready out of the box

## Headless Aware Editable
If your editable should work within the headless layout, you need to add some more stuff:

- Add `ToolboxHeadlessAwareBrickInterface`
- Add `headlessAction` method

Example:

```php
<?php

namespace App\Document\Area\Accordion;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class Accordion extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function action(Info $info): ?Response
    {
        $this->buildInfoParameters($info);

        return parent::action($info);
    }

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->buildInfoParameters($info, $headlessResponse);

        parent::headlessAction($info, $headlessResponse);
    }

    private function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        $infoParams = $info->getParams();
        
        $mySpecialParameter = '[AWESOME]';

        if ($headlessResponse instanceof HeadlessResponse) {
            // will be used in fe output
            $headlessResponse->addAdditionalConfigData('mySpecialParameter', $mySpecialParameter);

            return;
        }

        // will be used in editmode
        $info->setParam('mySpecialParameter', $mySpecialParameter);
    }
}
```

## Setup

```yaml

toolbox:
    theme:
        layout: !php/const ToolboxBundle\Manager\LayoutManagerInterface::TOOLBOX_LAYOUT_HEADLESS
        calculators:
            column_calculator: ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator # or your custom headless calculator
            slide_calculator: ToolboxBundle\Calculator\Bootstrap4\SlideColumnCalculator # or your custom headless calculator. slide columns aren't supported right now, so it doesn't matter which service you're using
```

## Headless Documents
It's possible to create simple headless documents:

- Only two types are supported: `areablock` and `area`

```yaml
toolbox:
    enabled_core_areas:
        - content
        - headline
        - image

    theme:
        layout: !php/const ToolboxBundle\Manager\LayoutManagerInterface::TOOLBOX_LAYOUT_HEADLESS
        headless_documents:
            index:
                areas:
                    indexHeadline:
                        type: area
                        areaType: headline
                    myBlock:
                        type: areablock
```

Add `ToolboxBundle\Controller\HeadlessController::headlessDocumentAction` as action to your document.