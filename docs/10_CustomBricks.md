# Custom Bricks 

There are three simple steps to create a custom Brick with a Toolbox context.

1. Add this configuration to `/config/packages/toolbox.yml`:

```yaml
# It's always a good idea to add your brick as a service.
services:
    App\Document\Areabrick\MyBrick\MyBrick:
        parent: ToolboxBundle\Document\Areabrick\AbstractAreabrick
        tags:
            - { name: toolbox.area.brick, id: my_brick }

toolbox:
    custom_areas:
        # that's the name of your brick. 
        my_brick:
            config_elements:
                title1:
                    type: input
                    title: That's a Title
                    description: Lorem Ipsum
                    # default config for input
                    # see: https://pimcore.com/docs/pimcore/10.0/Development_Documentation/Documents/Editables/Input.html#page_Configuration
                    config: ~
                title2:
                    type: input
                    title: That's also a Title
                    description: Lorem Ipsum
                    config: ~
```

2. Add the Area Class to `App/Document/Areabrick/MyBrick/MyBrick.php`:

```php
<?php

namespace App\Document\Areabrick\MyBrick;

use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class MyBrick extends AbstractAreabrick
{
    public function action(Info $info): ?Response
    {
        //call this to activate all the toolbox magic.
        parent::action($info);
    }

    public function getTemplateDirectoryName():string
    {
        // this method is only required if your brick name (e.g. my_brick or myBrick)
        // differs from the view template name (e.g. my-brick)
        
        return 'my-brick';
    }

    public function getTemplate(): string
    {
        // this method is only required if your brick name (e.g. my_brick or myBrick)
        // differs from the view template name (e.g. my-brick)
        
        return sprintf('areas/%s/view.%s', $this->getTemplateDirectoryName(), $this->getTemplateSuffix());
    }

    public function getName():string
    {
        return 'My Brick';
    }

    public function getDescription():string
    {
        return 'My Brick';
    }

    public function getIcon():string
    {
        return '/static/areas/my-brick/icon.svg';
    }
}
```

3. Add the view to `/templates/areas/my-brick/view.html.twig`:

```twig
<div class="my-brick {{ additionalClassesData|join(' ') }}">
    <h3>{{ pimcore_input('title1').data }}</h3>
    <p>{{ pimcore_input('title2').data }}</p>
</div>
```

That's it. If you want to refresh the permission table you also need to execute the `bin/console cache:clear` command.
