# Data Attributes Generator

The Toolbox Bundle provides a twig extension to generate data attributes for your html elements.

Since we're using the awesome [slick slider](http://kenwheeler.github.io/slick/) to generate the gallery element, the toolbox also relies on the data attributes generator.
There are two predefined nodes: `gallery` and `gallery_thumbs`. You may want to override them or add your custom nodes - so let's check it out:

### Define Attributes
 
```yaml
# add this to your app/config/config.yml
toolbox:
    dataAttributes:
    
        #override the gallery data attributes 
        gallery:
            lazy_load: false
            fade: false
           
        # all attributes must be written in underscore notation
        my_custom_node:
            attribute_1: true
            attribute_2: 'special value'
            
            # array values will be transformed to a json string
            responsive:
                -
                    breakpoint: 1200,
                    settings:
                        slidesToShow: 3

                -
                    breakpoint: 991,
                    settings:
                        slidesToShow: 2

                -
                    breakpoint: 480,
                    settings:
                        slidesToShow: 1
                        
```

### Usage:

Render the attributes is very simple. It's also possible to override your defined properties within the twig extension call.

```twig
<div class="x" {{ toolbox_data_attributes_generator('my_custom_node', {attribute_2: 'overritten special value'}) }}></div>
```

This will generate something like this:

```html
<div
 class="x" 
 data-attribute-1="true" 
 data-attribute-2="overritten special value" 
 data-responsive="[{"breakpoint":1200,"settings":{"slidesToShow":3}},{"breakpoint":991,"settings":{"slidesToShow":2}},{"breakpoint":480,"settings":{"slidesToShow":1}}]"
 ></div>

```