# Store Provider for (Multi-) Select Editables

This Bundle allows you to generate dynamic data for your dropdown elements. 
If you're in pimcore context, a typical store looks like this:

```yml
toolbox:
    areas:
        video:
            config_elements:
                a_classical_store:
                    type: multiselect
                    title: 'Test 2'
                    config:
                        store:
                            my_value: 'My Value'
                            foo: 'Bar'
```

### Configure Dynamic Store Provider
But you're also able to provide your data from a symfony service: 

```yml
toolbox:
    areas:
        video:
            config_elements:
                my_dynamic_store_provider:
                    type: select
                    title: 'Test'
                    config:
                        store_provider: 'my_awesome_store_provider'
```

### Register Custom Store Provider
Now we need to register our new service:

```yml
# app/config.yml
services:
    AppBundle\Toolbox\MyStoreProvider:
        tags:
            - { name: toolbox.editable.store_provider, identifier: 'my_awesome_store_provider' }

```

### Create Store Provider

```php
<?php

namespace AppBundle\Toolbox;

use ToolboxBundle\Provider\StoreProviderInterface;

class MyStoreProvider implements StoreProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return [
            'my_value' => 'My Value',
            'foo'      => 'Bar'
        ];
    }
}
```
