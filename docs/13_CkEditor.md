# CK-Editor Configuration

The Toolbox Bundle allows you to override and specifiy the ckeditor configuration.
There are several reasons for that:

- Keep the editor simple, remove unwanted dangerous elements from the ckeditor
- Define custom styles via configuration
- Use the same CK-Editor Layout in every wysiwyg element (Document, Emails, Objects)

## CK Editor Toolbar
The Toolbox Bundle already removes some (mostly dangerous) elements. Feel free to modify them according to your needs.
You may use the [online toolbar configurator](https://ckeditor.com/tmp/4.5.0-beta/ckeditor/samples/toolbarconfigurator/index.html#advanced) to do that.

> Info: This is the global configuration for the CK-Editor Toolbar.
> If you need to provide a different configuration in objects or areas, use the object/area configuration (see below)

> Info: It's possible to modify all the [CK-Editor Configuration](https://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-stylesSet)

### Overriding Configuration
The config node is a variableNode, so the values **doesn't get merged** with the default ones.
If your going to define the `toolbox -> ckeditor -> config` node, you must provide the complete configuration.

**Example** 
```yaml
toolbox:
    ckeditor:
        # with that node all the default values from toolbox configuration are overwritten now!
        config:
            uiColor: yellow # change the ui color of the editor for example
            toolbar:
              - name: basicstyles
                items:
                    - Bold
                    - "-"
                    - RemoveFormat
              - name: clipboard
                items:
                    - Cut
                    - Copy
                    - Redo
              - name: editing
                items:
                    - Replace
                    - "-"
                    - SelectAll
              - "/"
```

### Area CK-Editor Configuration
If you need to modify the toolbar configuration just for document areas, add this to your configuration:

**Example** 
```yaml
toolbox:
    ckeditor:
        area_editor:
            config:
                # this will merge the iframe element into the global toolbar configuration, if set
                toolbar:
                  -  name: insert
                     items:
                        - Iframe
```

### Object CK-Editor Configuration
If you need to modify the toolbar configuration just for objects, add this to your configuration:

**Example** 
```yaml
toolbox:
    ckeditor:
        object_editor:
            config:
                # this will merge the iframe element into the global toolbar configuration, if set
                toolbar:
                  -  name: insert
                     items:
                        - Iframe
                stylesSet: specialStyleSetForObjects # see global style sets configuration below
                uiColor: green

```

## CK Editor Global Style Sets
Define your custom styles here.

**Example**  
```yaml
toolbox:
    ckeditor:
        global_style_sets:
            default:
            -
                name: 'Dark Grey'
                element: [h1,h2,h3,h4,h5]
                attributes:
                    class: 'grey-1'
            -
                name: 'Lead'
                element: p
                attributes:
                    class: 'lead'
            specialStyleSetForObjects:
                name: 'Secret Color'
                element: p
                attributes:
                    class: 'secret-color'
```
