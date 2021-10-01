# Dynamic Link Element
The dynamic link extends the pimcore link element. It allows you to drag objects into the url field.
Of course it's impossible to generate a valid url from objects, so you need to do some further work.

> **WARNING!!** This Service has been deprecated.
> Please use the default Pimcore Link Tag which also allows object paths by using a [linkGenerator service](https://pimcore.com/docs/5.x/Development_Documentation/Objects/Object_Classes/Class_Settings/Link_Generator.html).
> If you want to migrate existing dynamic links read more about it [here](./70_ConfigurationFlags.md#-use_dynamic_links-flag) .

### Usage

```twig
{{ pimcore_dynamiclink('link', {'class' : 'btn btn-default teaser-link'}) }}
```
### Configuration 

#### Service
```yaml
# /app/config/services.yml
services:

    _defaults:
        autowire: true
        public: false

    App\EventListener\ObjectUrlListener:
        tags:
            - { name: kernel.event_listener, event: toolbox.dynamiclink.object.url, method: checkUrl }
```

#### Mapping

This mapper will transformed your dragged object path into a valid frontend path. 
You also need to setup a static route (in this case the `news_detail` route).

```php
<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Pimcore\Model\DataObject;

class ObjectUrlListener
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function checkUrl(GenericEvent $e)
    {
        if ( $e->getArgument('className') === 'news') {
            $obj = DataObject\News::getByPath($e->getArgument('path'));
            if ($obj instanceof DataObject\News) {

                $url = $this->generator->generate('news_detail', [
                    'text'   => $obj->getTitle(),
                    'id'     => $obj->getId(),
                    'newsId' => $obj->getId()
                ]);

                $e->setArgument('objectFrontendUrl', $url);
            }
        }
    }
}
```