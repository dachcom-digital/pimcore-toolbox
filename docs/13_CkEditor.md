# CK-Editor Configuration

The Toolbox Bundle allows you to override and specifiy the ckeditor configuration.
There are several reasons for that:

- Keep the editor simple, remove unwanted dangerous elements from the ckeditor
- Define custom styles via configuration
- Use the same CK-Editor Layout in every wysiwyg element (Document, Emails, Objects)

## CK Editor Toolbar
The Toolbox Bundle already removes some (mostly dangerous) elements. Feel free to modify them according to your needs.
You may use the [online toolbar configurator](https://ckeditor.com/latest/samples/toolbarconfigurator/index.html#basic) to do that.

> Info: This is the global configuration for the CK-Editor Toolbar.
> If you need to provide a different configuration in objects or areas, use the object/area configuration (see below)

> Info: It's possible to modify all the [CK-Editor Configuration](https://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-stylesSet)

### Overriding Configuration
The config node is a variableNode, so the values **don't get merged** with the default ones.
If you're going to define the `toolbox -> ckeditor -> config` node, you must provide the complete configuration.

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
If you only need to modify the toolbar configuration for document areas, add this to your configuration:

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
If you only need to modify the toolbar configuration for objects, add this to your configuration:

> **Note!** The object configuration does not respect different toolbox context environments at the moment. 
> Objects are not restricted to any sites by nature which makes any context-binding quite impossible.

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

## Area CK-Editor Plugins
Toolbox provides some CK-Editor Plugins:

### Google Opt Out
![ck-editor-google-opt-out](https://user-images.githubusercontent.com/700119/37820009-9dd6a418-2e7f-11e8-94b4-99c7a001a3a9.png)

Add a link to allow user to disable google analytics tracking.
There is also a [frontend Javascript Plugin](./80_Javascript.md).
