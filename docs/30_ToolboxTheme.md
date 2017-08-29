# Toolbox Theme / Layout

The Toolbox Bundle is currently using Bootstrap 3 as a supporting framework. It's, however, possible to create your custom layout / theme.

## Theme Configuration

This is the default theme configuration.

```yaml
toolbox:
    theme:
    
        # defines the view namespace [LAYOUT_NAME]
        layout: 'Bootstrap3'
        wrapper:
            image:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-xs-12'}
            columns:
                - {tag: 'div', class: 'row'}
            gallery:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-xs-12 col-gallery'}
            slideColumns:
                - {tag: 'div', class: 'row'}
            teaser:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-xs-12'}
                
```
#### Layout
This property defines the current layout view path.

#### Wrapper
Overriding templates are a great thing. Using the yaml configuration files is even better. With this property you have the power to wrap as many elements as you want around every toolbox element. As you can see, this bundle already adds some elements to specific elements.

## Views
All Views gets loaded from `@Toolbox/Toolbox/[LAYOUT_NAME]`.

## Calculators
There are two elements which needs some special calculation, depending on the current Grid-Framework: 

- Columns
- SlideColumns

Each elements needs a calculator to provide correct grid values. 

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

 # column calculator
toolbox.calculator.yourgridsystem.column:
    class: AppBundle\Calculator\ColumnCalculator
    public: false

# slide column calculator
toolbox.calculator.yourgridsystem.slide_column:
    class: AppBundle\Calculator\SlideColumnCalculator
    public: false

```

### Column Calculator

```php
<?php

namespace AppBundle\Calculator;
use ToolboxBundle\Calculator\ColumnCalculatorInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    public function calculateColumns($value, $columnConfiguration = [])
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

        //generate  column classes. please check out the bootstrap3 calculator if you need more information.
        return $columns;
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