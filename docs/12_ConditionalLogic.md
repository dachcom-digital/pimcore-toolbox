# Conditional Logic

Define some conditions to display/hide config elements.

Append the condition field. 
In this example, we only show the field `layout` if `type` selected value is "direct".

> condition change needs a reload of the edit window.

```yaml
toolbox:
    areas:
        teaser:
            config_elements:
                type:
                    type: select
                    title: 'Type'
                    config:
                        name: type
                        store:
                            direct: 'Direct'
                            snippet: 'Snippet'
                        default: direct
                            
                layout:
                    type: select
                    title: 'Layout'
                    conditions:
                        - type: direct
                    config:
                        store:
                            default: 'Default'
                        default: default
```