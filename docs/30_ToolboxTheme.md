# Toolbox Theme / Layout
The Toolbox Bundle is currently using Bootstrap 4 as default framework. 

## Supported Frameworks
- [Bootstrap 4](./themes/Bootstrap4.md)
- [Bootstrap 3](./themes/Bootstrap3.md)
- [UIkit3](./themes/UiKit3.md)
- [Headless](./themes/Headless.md)

***

It's also possible to create your custom layout / theme.

## Theme Configuration
Within the `theme` node you're able to define your custom layout information.

```yaml
toolbox:
    theme:
        grid: ~
        # defines the view namespace [LAYOUT_NAME]
        layout: !php/const ToolboxBundle\Manager\LayoutManagerInterface::TOOLBOX_LAYOUT_BOOTSTRAP4
        default_layout: false
        wrapper:
            image:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-12'}
            columns:
                - {tag: 'div', class: 'row'}
            gallery:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-12 col-gallery', attr: 'only-attributes-without-values-are-allowed'}
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
        default_layout: 'Bootstrap4'
```

```twig

{# example: Image #}

1. search layout: @Toolbox/Toolbox/Special/Image/view.html.twig
2. fallback layout: @Toolbox/Toolbox/Bootstrap3/Image/view.html.twig
```

> Note: `default_layout` is set to `false` by default, so no fallback layout gets loaded if the layout template is not available.

#### Wrapper
Overriding templates is a great thing. Using the yaml configuration files is even better. 
With this property you have the power to wrap as many elements as you want around every toolbox element. 
As you can see, this bundle already adds some wrappers to specific elements.

## Views
All Views get loaded from `@Toolbox/Toolbox/[LAYOUT_NAME]`.

## Calculators
There are two elements which needs some special calculation, depending on the current Grid-Framework: 

- Columns
- SlideColumns

Each element needs a calculator to provide correct grid values. 

## Custom Layout Implementation
So let's assume that you want to add your own custom grid framework:


```yaml
# config/packages/toolbox.yaml
toolbox:
    theme:
        layout: 'YourGridSystem'
        wrapper:
            image:
                - {tag: 'div', class: 'grid-row'}
                - {tag: 'div', class: 'col 12-columns'}
```

From now on, the Toolbox Bundle will search every element view in `@ToolboxBundle/Resources/views/Toolbox/YourGridSystem/*`.
Now implement the calculators (don't forget the tags):

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        public: false

     # column calculator
    App\Calculator\ColumnCalculator:
        calls:
            - [setConfigManager, ['@ToolboxBundle\Manager\ConfigManager']]
        tags:
            - { name: toolbox.calculator, type: column }
    
    # slide column calculator
    App\Calculator\SlideColumnCalculator:
        tags:
            - { name: toolbox.calculator, type: slide_column }
```

```yaml
# config/packages/toolbox.yaml
toolbox:
    theme:
        calculators:
            column_calculator: App\Calculator\ColumnCalculator
            slide_calculator: App\Calculator\SlideColumnCalculator
```

### Column Calculator

```php
<?php

namespace App\Calculator;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;

class ColumnCalculator implements ColumnCalculatorInterface
{
    protected $configManager;

    public function setConfigManager(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;

        return $this;
    }

    public function calculateColumns($value, $columnConfiguration = [])
    {
        $themeSettings = $this->configManager->getConfig('theme');
        $gridSettings = $themeSettings['grid'];
        $gridSize = $gridSettings['grid_size'];

        $columns = [];

        // generate column classes here.
        // please check out the bootstrap4 calculator if you need more information.

        return $columns;
    }
    
    public function getColumnInfoForAdjuster($value, $columnConfiguration = null)
    {
         // generate column info for the column adjuster.
         // please check out the bootstrap4 calculator if you need more information.
    }
}
```

### Slide Column Calculator

```php
<?php

namespace App\Calculator;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    public function calculateSlideColumnClasses($columnType, $columnConfiguration)
    {
        // generate slide column classes here.
        // please check out the bootstrap3 slide column calculator if you need more information.
        return [];
    }
}
```
