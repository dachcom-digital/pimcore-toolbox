# Code Style

### CamelCase
All Toolbox Elements are written in camelcase.

**Controller**  
- `Document\Areabrick\GoogleMap` 

### Underscore
All Configuration Elements are written in underscore:

```yaml
toolbox:
    areas:
        anchor:
            config_elements:
                anchor_name: # underscore: in view, you'll call it with pimcore_input('anchor_name');
                    type: input
                    title: Anchor Name
                    config: ~
                anchor_title: # underscore: in view, you'll call it with pimcore_input('anchor_title');
                    type: input
                    title: Anchor Title
                    config: ~

```

### Underscore Exceptions
- Google Maps Element, Map Options: Values must to be camelcase because of the google map configuration mapping.
- Config Elements `config` Tree: Values must be camelcase because of the pimcore element configuration mapping. 

### Strings
- Do not use percentage values in titles
- Always quote your title values