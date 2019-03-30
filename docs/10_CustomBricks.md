# Custom Bricks 

There are three simple steps to create a custom Brick with a Toolbox context.

1. Add this configuration to `/app/config/pimcore/config.yml`:

> **Tip:** Add this to a separate config file.

### Code Style
> **Important!** Don't use dashes in your area name! Always use Camel- or Snake Case!
> Example: Instead of `my-brick` you need to define your custom area as `my_brick` or `myBrick`.
> Since it's symfony standard, we recommend to use the underscore strategy!
 
```yaml
# It's always a good idea to add your brick as a service.
services:
    AppBundle\Document\Areabrick\MyBrick\MyBrick:
        parent: ToolboxBundle\Document\Areabrick\AbstractAreabrick
        calls:
            # set the brick type to external
            - [setAreaBrickType, ['external']]
        tags:
            - { name: pimcore.area.brick, id: my_brick }

toolbox:
    custom_areas:
        # that's the name of your brick. 
        my_brick:
            config_elements:
                title1:
                    type: input
                    title: That's a Title
                    col_class: t-col-half
                    description: Lorem Ipsum
                    # default config for input
                    # see: https://www.pimcore.org/docs/5.0.0/Documents/Editables/Input.html#page_Configuration
                    config: ~
                title2:
                    type: input
                    title: That's also a Title
                    col_class: t-col-half
                    description: Lorem Ipsum
                    config: ~
```

2. Add the Area Class to `AppBundle/Document/Areabrick/MyBrick/MyBrick.php`:

```php
<?php

namespace AppBundle\Document\Areabrick\MyBrick;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class MyBrick extends AbstractAreabrick
{
    public function action(Info $info)
    {
        //call this to activate all the toolbox magic.
        parent::action($info);
    }

    public function getName()
    {
        return 'My Brick';
    }

    public function getDescription()
    {
        return 'My Brick';
    }

    public function getIcon()
    {
        return '/static/areas/my_brick/icon.svg';
    }
}
```

3. Add the view to `/app/Resources/views/Areas/my_brick/view.html.twig`:

```twig
{{ elementConfigBar|raw }}
<div class="my-brick">
    <h3>{{ pimcore_input('title1').getData() }}</h3>
    <p>{{ pimcore_input('title2').getData() }}</p>
</div>
```

### Adding Bricks from Bundles

If you want to add bricks from other bundles, eg. `src/DemoBundle` and you want to load the view from the bundle directory itself, then you have to override the template location:

```php
<?php

namespace DemoBundle\Document\Areabrick\MyBrick;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class MyBrick extends AbstractAreabrick
{
    public function getTemplateLocation()
    {
        return static::TEMPLATE_LOCATION_BUNDLE;
    }
}
```

That's it. Sometimes you need to clear your cache, if the Brick won't show up.
