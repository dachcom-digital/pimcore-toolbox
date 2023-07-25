# Bootstrap 3 Theme
This is the legacy setup for bootstrap 3.

## Setup

```yaml

toolbox:
    theme:
        layout: 'Bootstrap3'
        calculators:
            column_calculator: ToolboxBundle\Calculator\Bootstrap3\ColumnCalculator
            slide_calculator: ToolboxBundle\Calculator\Bootstrap3\SlideColumnCalculator
        wrapper:
            googleMap:
                - { tag: 'div', class: 'embed-responsive embed-responsive-16by9' }
            video:
                - { tag: 'div', class: 'embed-responsive embed-responsive-16by9' }
            image:
                - { tag: 'div', class: 'row' }
                - { tag: 'div', class: 'col-xs-12' }
            columns:
                - { tag: 'div', class: 'row' }
            gallery:
                - { tag: 'div', class: 'row' }
                - { tag: 'div', class: 'col-xs-12 col-gallery' }
            slideColumns:
                - { tag: 'div', class: 'row' }
            teaser:
                - { tag: 'div', class: 'row' }
                - { tag: 'div', class: 'col-xs-12' }
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
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    {% block content %}
                        {{ pimcore_areablock('content', toolbox_areablock_config('content')) }}
                    {% endblock %}
                </div>
            </div>
        </div>
    </div>
        
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
```