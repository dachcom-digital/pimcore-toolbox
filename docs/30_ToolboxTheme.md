# Toolbox Theme / Layout
The Toolbox Bundle is currently using Bootstrap 4 as a supporting framework. It's, however, possible to create your custom layout / theme.

## Theme Configuration
This is the default theme configuration.

```yaml
toolbox:
    theme:
        grid: ~
        # defines the view namespace [LAYOUT_NAME]
        layout: 'Bootstrap4'
        default_layout: false
        wrapper:
            image:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-12'}
            columns:
                - {tag: 'div', class: 'row'}
            gallery:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-12 col-gallery'}
            slideColumns:
                - {tag: 'div', class: 'row'}
            teaser:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-12'}
                
```
#### Grid
Please [read this](60_ColumnAdjuster.md) to learn more about the fancy column adjuster - where this setting is important!

#### Layout
This property defines the current layout view path.

#### Default Layout
Define a fallback layout. If you're using a custom layout and you have defined a default layout, the template routing will work like this:

```yaml
toolbox:
    theme:
        layout: 'Special'
        default_layout: 'Bootstrap3'
```

```twig

{# example: Image. #}

1. search layout: @Toolbox/Toolbox/Special/Image/view.html.twig
2. fallback layout: @Toolbox/Toolbox/Bootstrap3/Image/view.html.twig
```

> Note: `default_layout` is set to `false` by default, so no fallback layout gets loaded if the layout template is not available.

#### Wrapper
Overriding templates are a great thing. Using the yaml configuration files is even better. With this property you have the power to wrap as many elements as you want around every toolbox element. As you can see, this bundle already adds some wrappers to specific elements.

## Views
All Views gets loaded from `@Toolbox/Toolbox/[LAYOUT_NAME]`.

## Calculators
There are two elements which needs some special calculation, depending on the current Grid-Framework: 

- Columns
- SlideColumns

Each element needs a calculator to provide correct grid values. 

## Custom Layout Implementation
So let's assume that you want to add your own custom grid framework:


```yaml
# /app/config/config.yml
toolbox:
    theme:
        layout: 'YourGridSystem'
        wrapper:
            image:
                - {tag: 'div', class: 'grid-row'}
                - {tag: 'div', class: 'col 12-columns'}
```

From now on, the Toolbox Bundle will search every element view in `@ToolboxBundle/Resources/views/Toolbox/YourGridSystem/*`.
Now implement the calculators:

```yaml
# /app/config/services.yml

services:
    _defaults:
        autowire: true
        public: false

     # column calculator
    AppBundle\Calculator\ColumnCalculator: ~
    
    # slide column calculator
    AppBundle\Calculator\SlideColumnCalculator: ~
```

```yaml
# /app/config/config.yml
toolbox:
    theme:
        calculators:
            ToolboxBundle\Calculator\ColumnCalculator: AppBundle\Calculator\ColumnCalculator
            ToolboxBundle\Calculator\SlideColumnCalculator: AppBundle\Calculator\SlideColumnCalculator
```

### Column Calculator

```php
<?php

namespace AppBundle\Calculator;
use ToolboxBundle\Calculator\ColumnCalculatorInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    public function calculateColumns($value, $columnConfiguration = [], $gridSize = 12)
    {
        $columns = [];

        //$value: selected column value from column.
        $t = explode('_', $value);

        $_columns = array_splice($t, 1);

        //define your column classes.
        $bootstrapClassConfig = [];

        foreach ($_columns as $i => $columnClass) {

            $columns[] = [
                'columnClass' => join(' ', $bootstrapClassConfig),
                'columnType'  => $value,
                'name'        => 'column_' . $i
            ];
        }

        // generate column classes.
        // please check out the bootstrap4 calculator if you need more information.
        return $columns;
    }
    
    public function getColumnInfoForAdjuster($currentColumn = '', $columnConfiguration = [], $gridSettings = []) {
         // generate column info for the column adjuster.
         // please check out the bootstrap4 calculator if you need more information.
    }
}
```

### Slide Column Calculator

```php
<?php

namespace AppBundle\Calculator;
use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    public function calculateSlideColumnClasses($columnType, $columnConfiguration)
    {
        //generate slide column classes. please check out the bootstrap3 calculator if you need more information. 
        return [];
    }
}
```