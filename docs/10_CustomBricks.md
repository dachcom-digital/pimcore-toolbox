# Custom Bricks 

If you want to have full control about your area brick, create a [default brick](./10_CustomBricks.md#default-brick).
If you just want to create a simple brick, create a [simple brick](./10_CustomBricks.md#simple-brick). With simple bricks you don't have to create any php classes at all but.

- [Default Brick](./10_CustomBricks.md#default-brick)
- [Simple Brick](./10_CustomBricks.md#simple-brick)

## Default Brick

There are three simple steps to create a custom brick:

1. Add this configuration to `/config/packages/toolbox.yml`:

```yaml
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

2. Add the area class to `App/Document/Areabrick/MyBrick/MyBrick.php`:

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

***

## Simple Brick

Two steps to create a simple brick:

1. Add this configuration to `/config/packages/toolbox.yml`:

```yaml
services:

    # this is the fastest way to create a simple brick. 
    # template needs to be located in /template/areas/my_simple_brick/view.html.twig
    app.brick.my_simple_brick: 
        parent: ToolboxBundle\Document\Areabrick\AbstractAreabrick
        tags:
            - {  name: toolbox.area.simple_brick, id: my_simple_brick, title: 'My Simple Brick' }
              
    # you may want to define some more info: description, template path and icon 
    app.brick.my_second_simple_brick:
        parent: ToolboxBundle\Document\Areabrick\AbstractAreabrick
        tags:
            - {
                name: toolbox.area.simple_brick,
                id: my_scond_simple_brick,
                title: 'My Second Simple Brick',
                description: 'Some Description',
                template: 'areas/my-second-simple-brick/view.html.twig',
                icon: '/public/path/to/your/icon.svg'
            } 
                
toolbox:
    custom_areas:
        # that's the name of your simple brick. 
        # configuration behaves the same as a default brick 
        my_simple_brick:
            config_elements:
                title:
                    type: input
                    title: That's a Title
                    description: Lorem Ipsum
                    config: ~
```

2. Add the view to `/templates/areas/my_simple_brick/view.html.twig`:

```twig
<div class="my-brick {{ additionalClassesData|join(' ') }}">
    <span>{{ pimcore_input('title').data }}</span>
</div>
```

That's it. If you want to refresh the permission table you also need to execute the `bin/console cache:clear` command.
