# Headless
Learn, how to put toolbox into headless mode.

## Thinks to know
- The Headless layout will super simplify the backend edit mode. No views will be generated there, but only the configuration fields.
- In the frontend layer, the editable output will be a simple array (already nested)
- All toolbox editables except `parallax` and `slidecolumn` are headless ready out of the box
- Since the output is json, you're not able to add or modify data in view. Use the `HEADLESS_EDITABLE_ACTION` event to add more data and use a tagged `toolbox.property.normalizer` to change the json output

## Headless Aware Editable
If your editable should work within the headless layout, you need to add some more stuff:

- Add `ToolboxHeadlessAwareBrickInterface`
- Add `headlessAction` method

Example:

```php
<?php

namespace App\Document\Area\MyHeadlessEditable;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

class MyHeadlessEditable extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
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

***

## Headless Documents
It's possible to create simple headless documents:

```yaml
toolbox:
    enabled_core_areas:
        - content
        - headline
        - image

    theme:
        layout: !php/const ToolboxBundle\Manager\LayoutManagerInterface::TOOLBOX_LAYOUT_HEADLESS
        headless_documents:
            
            default_headless_document:
                areas:
                    index_headline:
                        type: area
                        config:
                            type: headline
                    myBlock:
                        type: areablock

            another_headless_document:
                areas:
                    headline:
                        type: area
                        config:
                            type: headline
                    wysiwyg:
                        type: area
                        config:
                            type: content
                    simple_input:
                        type: input
                        title: 'My Simple Input'
                    awesome_blocks:
                        type: block
                        children:
                            block_text:
                                type: input
                                title: 'Text'
                            block_link:
                                type: link
                                property_normalizer: ToolboxBundle\Normalizer\LinkNormalizer
                                title: 'Link'
                                config:
                                    class: 'list-link'
                    special_accordion:
                        type: area
                        config:
                            type: accordion
```

- Add `ToolboxBundle\Controller\HeadlessController::headlessDocumentAction` as action to your document.
- Add a text property to the document called `headless_document` and set `default_headless_document` as value. You can create multiple documents that way.

***

## Extend existing headless editables
Just register an event listener to `ToolboxBundle::HEADLESS_EDITABLE_ACTION` which will provide you an `HeadlessEditableActionEvent` object 
where you're allowed to add additional data to the `HeadlessResponse` object

## Normalize data
In headless context, you're not able to modify output via view files and in some scenarios you also want to output data differently in a given context.

Toolbox comes with some pre-configured normalizers:

### DownloadRelationsNormalizer
This normalizer will transform download relations to meaningful data arrays (like the `toolbox_download_info` twig helper)

### GalleryRelationsNormalizer
This normalizer will transform gallery relations to thumbnail data arrays

### ImageEditableNormalizer
This normalizer will transform the inline image editable to a thumbnail data array

### Custom normalizer
First, you need to add your normalizer:

```yaml
services:
    App\Normalizer\MyNormalizer:
        tags:
            - { name: toolbox.property.normalizer }
```

```php
<?php

namespace App\Normalizer;

use ToolboxBundle\Normalizer\PropertyNormalizerInterface;

class MyNormalizer implements PropertyNormalizerInterface
{
    public function normalize(mixed $value, ?string $toolboxContextId = null): mixed
    {
        // @todo: normalize your data
        
        return $value;
    }
}
```

Then, assign them:
```yaml
toolbox:
    areas:
        my_area:
            config_elements:
                simple_config:
                    type: checkbox
                    title: 'Super Checkbox'
                    config: ~
                    property_normalizer: App\Normalizer\MyNormalizer # normalize the config value
            config_parameter: ~
            inline_config_elements:
                inline_relations:
                    type: relations
                    config: ~
            additional_property_normalizer:
                myAdditionalProperty: App\Normalizer\MyNormalizer # normalize your config property, added in your "headlessAction() method
```

### Default normalizer definition
If you want to apply a normalizer to every element of type `checkbox`, you're able to define it globally:

```yaml
toolbox:
    property_normalizer:
        default_type_mapping:
            checkbox: App\Normalizer\MyNormalizer
```

> It is still possible, to override the default mapping by using the `property_normalizer` in your element config
> since those will be processed with the highest priority!