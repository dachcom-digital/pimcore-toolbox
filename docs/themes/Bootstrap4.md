# Bootstrap 4 Theme
This is the default theme so there is not too much to configure.

## Setup

```yaml

toolbox:
    theme:
        grid:
            grid_size: 12
            breakpoints:
            -   identifier: 'xs'
                name: 'Breakpoint: XS'
                description: 'Your Description'
            -   identifier: 'ty'
                name: 'Breakpoint: TY'
                description: 'Your Description'
            -   identifier: 'sm'
                name: 'Breakpoint: SM'
                description: 'Your Description'
            -   identifier: 'md'
                name: 'Breakpoint: MD'
                description: 'Your Description'
            -   identifier: 'lg'
                name: 'Breakpoint: LG'
                description: 'Your Description'
            -   identifier: 'xl'
                name: 'Breakpoint: XL'
                description: 'Your Description'
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
                <div class="col-12">
                    {% block content %}
                        {{ pimcore_areablock('content', toolbox_areablock_config('content')) }}
                    {% endblock %}
                </div>
            </div>
        </div>
    </div>
        
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" >
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
```