# UiKit Theme

## Setup

```yaml

toolbox:
    theme:
        layout: 'UIkit3'
        calculators:
            column_calculator: ToolboxBundle\Calculator\UIkit3\ColumnCalculator
            slide_calculator: ToolboxBundle\Calculator\UIkit3\SlideColumnCalculator
        wrapper:
            image: ~
            gallery: ~
            slideColumns: ~
            teaser: ~
            columns:
                  - {tag: 'div', class: 'uk-grid'}
        grid:
            grid_size: 12
            column_store:
                11: '1/1'
                12: '1/2'
                13: '1/3'
                14: '1/4'
                15: '1/5'
                16: '1/6'
                23: '2/3'
                25: '2/5'
                34: '3/4'
                35: '3/5'
                45: '4/5'
                56: '5/6'
            breakpoints:
                -   identifier: 's'
                    name: 'Breakpoint: S'
                    description: 'Your Description'
                -   identifier: 'm'
                    name: 'Breakpoint: M'
                    description: 'Your Description'
                -   identifier: 'l'
                    name: 'Breakpoint: L'
                    description: 'Your Description'
                -   identifier: 'xl'
                    name: 'Breakpoint: XL'
                    description: 'Your Description'
    areas:
        columns:
            config_elements:
                type:
                    type: select
                    title: 'Columns'
                    config:
                        store:
                            column_11: '1 Column'
                            column_13_23: '2 Columns (33:66)'
                            column_23_13: '2 Columns (66:33)'
                            column_14_34: '2 Columns (25:75)'
                            column_34_14: '2 Columns (75:25)'
                            column_12_12: '2 Columns (50:50)'
                            column_13_13_13: '3 Columns (33:33:33)'
                        default: column_12_12
                equal_height: ~
                additional_classes: ~ 
        slideColumns:
            config_elements:
                slides_per_view:
                    type: select
                    title: 'Slides per View'
                    config:
                        store:
                            '2': '2 Columns'
                            '3': '3 Columns'
                            '4': '4 Columns'
                            '6': '6 Columns'
                        default: '4'
                equal_height: ~
                additional_classes:  ~  
            config_parameter:
                column_classes: ~
                breakpoints: []
```

## Layout

```html
<!DOCTYPE html>
<html lang="{{ app.request.locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{ pimcore_head_title() }}
    {{ pimcore_head_meta() }}
    {{ pimcore_head_link() }}
</head>

<body>
    
    <div id="site">
    
        <div class="uk-container uk-margin-bottom">
            <div class="uk-cover-container uk-height-medium">
                <img src="https://getuikit.com/docs/images/dark.jpg" alt="" uk-cover>
            </div>
        </div>
    
        {% block content %}
            {{ pimcore_areablock('content', toolbox_areablock_config('content')) }}
        {% endblock %}
    
    </div>
    
    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.5.9/dist/css/uikit.min.css"/>
    <!-- UIkit JS -->
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.5.9/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.5.9/dist/js/uikit-icons.min.js"></script>
    
    <-- Optional: Toolbox Core -->
    <script>
        $.toolboxCore();
    </script>

</body>
</html>
```