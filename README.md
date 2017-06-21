# Pimcore 5 Toolbox

> This Toolbox Repo is for pimcore 5 only and under heavy development. 

The Toolbox is a Kickstarter for your every day project. It provides some important bricks and structure basics which allows rapid and quality-orientated web development. 

![bildschirmfoto 2017-06-21 um 09 30 29](https://user-images.githubusercontent.com/700119/27372271-541e6106-5664-11e7-9159-7f4aefa26cb6.png)


### What's the meaning of Toolbox?

- create often used bricks in a second
- extend, override toolbox bricks 
- add config elements via yml configuration
- add consistent and beautiful config elements
- implement conditions to your config (for example: display a dropdown in config window if another checkbox has been checked)
- add your custom bricks while using the toolbox config environment
- removes the default `pimcore_area_*` element wrapper from each brick

### And what's not?
- It's not a Avada Theme. While the Toolbox provides some basic Javascript for you, you need to implement and mostly modifiy them by yourself.
- Toolbox supports only the twig template engine, so there is no way to activate the php template engine (and there will never be such thing).

## Asset Management

```
/var/www bin/console assets:install --symlink
```

**Frontend JS Implementation**  
Add the sources to your `gulpfile.js` or add it with plain html. For example:
```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/vendor/vimeo-api.min.js')}}" ></script>
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/toolbox-main.js')}}" ></script>
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/toolbox-video.js')}}" ></script>
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/toolbox-googleMaps.js')}}" ></script>
```

## Bricks

### Toolbox Bricks 

The Toolbox provides a lot of ready-to-use Bricks:

- Accordion
- Anchor
- Columns
- Container
- Content
- Download
- Gallery
- Google Map
- Headline
- Image
- Link List
- Parallax Container
- Parallax Container Section
- Separator
- Slide Columns
- Spacer
- Teaser
- Video

### Custom Bricks 

There are three simple steps to create a custom Brick with a Toolbox context.

1. Add this configuration to `AppBundle/Resources/config/pimcore/config.yml`:

> **Tip:** Add this to a separate config file.

```yaml

# It's always a good idea to add your brick as a service.
services:
    toolbox.area.brick.myBrick:
        parent: toolbox.area.brick.base_brick
        class: AppBundle\Document\Areabrick\MyBrick\MyBrick
        calls:
            # set the brick type to external
            - [setAreaBrickType, ['external']]
        tags:
            - { name: pimcore.area.brick, id: myBrick }

toolbox:
    custom_areas:
        # that's the name of your brick. 
        myBrick:
            configElements:
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
}
```

3. Add the view to `AppBundle/Resources/views/Areas/MyBrick/view.html.twig`:

```twig
{{ elementConfigBar|raw }}
<div class="my-brick">
    <h3>{{ pimcore_input('title1').getData() }}</h3>
    <p>{{ pimcore_input('title2').getData() }}</p>
</div>
```

That's it. Sometimes you need to clear you cache, if the Brick won't show up.

## i18n
TBD.

## Pimcore Fixes / Overrides
- fix the pimcore iframe [maskFrames](src/ToolboxBundle/Resources/public/js/document/edit.js#L8)   bug (in somecases the iframe overlay field does not apply to the right position)
- Transforms all the brick config buttons (`pimcore_area_edit_button_*`) to more grateful ones.

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)